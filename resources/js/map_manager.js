import * as am5 from "@amcharts/amcharts5";
import * as am5map from "@amcharts/amcharts5/map";

const activeMapRoots = new WeakSet();

// 1. Create a global observer that watches the entire document for DOM changes
const globalMapObserver = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
        mutation.addedNodes.forEach(function (node) {
            if (node.nodeType === Node.ELEMENT_NODE) {
                const maps = node.classList?.contains('interactive-map') 
                    ? [node] 
                    : node.querySelectorAll('.interactive-map');

                maps.forEach(function (container) {
                    if (activeMapRoots.has(container)) return;
                    activeMapRoots.add(container);
                    
                    loadMapConfig(container);
                });
            }
        });

        if (mutation.type === "attributes" && mutation.attributeName === "data-config-endpoint") {
            const container = mutation.target;
            if (container.classList.contains('interactive-map')) {
                loadMapConfig(container);
            }
        }
    });
});
$(document).ready(function(){
    globalMapObserver.observe(document.body, { 
        childList: true,
        subtree: true,   
        attributes: true, 
        attributeFilter: ["data-config-endpoint"]
    });

    document.querySelectorAll('.interactive-map').forEach(function (container) {
        loadMapConfig(container);
    });
});

function loadMapConfig(container){
    var configEndpoint = container.getAttribute("data-config-endpoint");
    if (!configEndpoint) return;
    fetch(configEndpoint, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(config => {
        var oldScript = document.querySelector(`script[src="${config.js_path}"]`);
        if (oldScript) oldScript.remove();

        var mapScript = document.createElement("script");
        mapScript.src = config.js_path;
        
        mapScript.onload = function() {
            initializeMap(config, container);
        };
        
        document.head.appendChild(mapScript);
    })
    .catch(error => console.error("Security/Configuration loading error:", error));

}

function initializeMap(config, container){
    am5.ready(function() {
        console.log(container)
        var containerId = container.id;

        if (activeMapRoots[containerId]) {
            activeMapRoots[containerId].dispose();
            delete activeMapRoots[containerId];
        }

        container.innerHTML = ""; 

        var root = am5.Root.new(containerId);
        
        activeMapRoots[containerId] = root;

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
            fill: am5.color("#1b9dbd"), 
            stroke: am5.color("#cbd1c2"), 
            strokeWidth: 1.5
        });

        if(config.mode == "create"){
            polygonSeries.mapPolygons.template.setAll({
                tooltipText: "{name}",
            });
        }

        polygonSeries.mapPolygons.template.states.create("active", {
            fill: am5.color("#ecfc0e")
        });

        var lastSelectedPolygon;

        if(config.mode!='question' && config.mode!='result'){
            polygonSeries.mapPolygons.template.states.create("hover", {
                fill: am5.color("#9de7b6")
            });

            polygonSeries.events.on("datavalidated", function() {
                var parentWrapper = container.closest('.dynamic-field-map');
                if (parentWrapper) {
                    var hiddenInput = parentWrapper.querySelector('.selected-target');
                    if (hiddenInput && hiddenInput.value !== "") {
                        var dataItem = polygonSeries.getDataItemById(hiddenInput.value);
                        if (dataItem) {
                            var targetPolygon = dataItem.get("mapPolygon");
                            if (targetPolygon) {
                                lastSelectedPolygon = targetPolygon;
                                lastSelectedPolygon.set("active", true);
                            }
                        }
                    }
                }
            });

            
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
                var parentWrapper = container.closest('.dynamic-field-map');

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

                if (parentWrapper) {
                    var hiddenInput = parentWrapper.querySelector('.selected-target');
                    if (hiddenInput) {
                        hiddenInput.value = dataContext.id;
                    }
                }
            });
        }
        if(config.mode =='question'){
            polygonSeries.events.on("datavalidated", function() {
                var hiddenInput = config.target;
                if (hiddenInput && hiddenInput.value !== "") {
                    var dataItem = polygonSeries.getDataItemById(hiddenInput);
                    if (dataItem) {
                        var targetPolygon = dataItem.get("mapPolygon");
                        if (targetPolygon) {
                            targetPolygon.set("active", true);
                        }
                    }
                }
            });
        }
        if(config.mode =='result'){
            polygonSeries.events.on("datavalidated", function() {
                var correctTargetId = config.target;      
                var userAnswerId = config.user_answer;

                if (correctTargetId && correctTargetId !== "") {
                    var correctDataItem = polygonSeries.getDataItemById(correctTargetId);
                    if (correctDataItem) {
                        var correctPolygon = correctDataItem.get("mapPolygon");
                        if (correctPolygon) {
                            correctPolygon.set("fill", am5.color("#3be21a")); // Bootstrap success green
                        }
                    }
                    if (userAnswerId && userAnswerId !== "" && userAnswerId !== correctTargetId) {
                        var userNonCorrectDataItem = polygonSeries.getDataItemById(userAnswerId);
                        if (userNonCorrectDataItem) {
                            var userPolygon = userNonCorrectDataItem.get("mapPolygon");
                            if (userPolygon) {
                                userPolygon.set("fill", am5.color("#dc3545"));
                            }
                        }
                    }
                }
            });
        }

        polygonSeries.events.on("datavalidated", function() {
            chart.goHome();
        });
        
    });
};
