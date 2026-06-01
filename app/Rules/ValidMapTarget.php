<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Illuminate\Support\Facades\Http;
use App\Models\Map;

class ValidMapTarget implements ValidationRule
{
    protected ?string $mapId;

    public function __construct(?string $mapId)
    {
        $this->mapId = $mapId;
    }
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->mapId) {
            return;
        }

        $map = Map::find($this->mapId);
        if (!$map) {
            $fail('The selected map infrastructure does not exist.');
            return;
        }

        $jsUrl = $map->js_path;
        $prefix = "https://cdn.amcharts.com/lib/5/geodata/";
        $mapName = str_replace($prefix, "", $jsUrl); 
        $mapName = str_replace(".js", ".json", $mapName); 
        $jsonUrl = "https://cdn.amcharts.com/lib/5/geodata/json/".$mapName;

        try {
            $response = Http::timeout(3)->get($jsonUrl);

            if (! $response->successful()) {
                $fail('The selected map could not be retrieved.');
                return;
            }
        } catch (\Exception $e) {
            $fail('There was an exception when retrieving the map retrieved.');
            return;
        }

        $fileContents = $response->body();
        $validIds = $this->extractValidIds($fileContents);

        if (!in_array($value, $validIds)) {
            $fail("The selected region \"{$value}\" is invalid for the chosen map.");
        }
    }

    protected function extractValidIds(string $contents){
        $mapData = json_decode($contents, true);

        $validIds = collect($mapData['features'] ?? [])
            ->pluck('id') 
            ->filter()
            ->toArray(); 
        return $validIds; 
    }
}
