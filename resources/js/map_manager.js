import * as am5 from "@amcharts/amcharts5";
import * as am5map from "@amcharts/amcharts5/map";

document.addEventListener('DOMContentLoaded', function () {
    var chartContainer = document.getElementById("chartdiv");
    if (!chartContainer) return;

    var configEndpoint = chartContainer.getAttribute("data-config-endpoint");
    if (!configEndpoint) return;

    fetch(configEndpoint, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(config => {
        var mapScript = document.createElement("script");
        mapScript.src = config.js_path;
        
        mapScript.onload = function() {
            initializeMap(config);
        };
        
        document.head.appendChild(mapScript);
    })
    .catch(error => console.error("Security/Configuration loading error:", error));
});

function initializeMap(config){
    am5.ready(function() {
        
        am5.array.each(am5.registry.rootElements, function(re) {
            if (re.get("id") === "chartdiv") {
                re.dispose();
            }
        });

        var root = am5.Root.new("chartdiv");

        var jsUrl = config.js_path;
        var prefix = "https://cdn.amcharts.com/lib/5/geodata/";
        var mapName = jsUrl.replace(prefix, '').replaceAll('/', '_').replace('.js', ''); 
        var amChartsVariableName = "am5geodata_" + mapName;

        var chart = root.container.children.push(
            am5map.MapChart.new(root, {
                panX: "translateX",
                panY: "translateY",
                projection: am5map.geoMercator(),
                paddingLeft: 0,
                paddingRight: 0,
                paddingTop: 0,
                paddingBottom: 0
            })
        );

        chart.set("zoomControl", am5map.ZoomControl.new(root, {}));

        var polygonSeries = chart.series.push(
            am5map.MapPolygonSeries.new(root, {
                geoJSON: window[amChartsVariableName] 
            })
        );
        
        polygonSeries.mapPolygons.template.setAll({
            interactive: true,
            fill: am5.color("#1bbd51"), 
            stroke: am5.color("#cbd1c2"), 
            strokeWidth: 1.5
        });

        if(config.mode == "create"){
            polygonSeries.mapPolygons.template.setAll({
                tooltipText: "{name}",
            });
        }

        polygonSeries.mapPolygons.template.states.create("hover", {
            fill: am5.color("#9de7b6")
        });

        polygonSeries.mapPolygons.template.states.create("active", {
            fill: am5.color("#1b26bd")
        });

        var lastSelectedPolygon;
        polygonSeries.mapPolygons.template.events.on("click", function(ev) {
            if (lastSelectedPolygon && lastSelectedPolygon !== ev.target) {
                lastSelectedPolygon.set("active", false);
            }

            var polygon = ev.target;
            polygon.set("active", true);
            lastSelectedPolygon = polygon;

            var dataContext = polygon.dataItem.dataContext;

            var placeholder = document.getElementById('inspector-placeholder');
            var inspector = document.getElementById('inspector-data');
            var name = document.getElementById('selected-name');
            var id = document.getElementById('selected-id');

            if(placeholder){
                placeholder.style.display = 'none';
            }

            if(inspector){
                inspector.style.display = 'block';
            }

            if(name){
                name.innerText = dataContext.name;
            }

            if(id){
                id.innerText = dataContext.id;
            }
        });

        
        polygonSeries.events.on("datavalidated", function() {
            chart.goHome();
        });
        
    });
};
