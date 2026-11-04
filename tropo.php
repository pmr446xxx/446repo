<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$googleMapsApiKey = getenv('GOOGLE_MAPS_API_KEY') ?: '';
$mapsConfigPath = __DIR__ . '/includes/google-maps-config.php';
if (is_file($mapsConfigPath)) {
    $mapsConfig = require $mapsConfigPath;
    if (is_array($mapsConfig) && !empty($mapsConfig['api_key'])) {
        $googleMapsApiKey = (string) $mapsConfig['api_key'];
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#070d12"><title>446dx.pl — Aktualne warunki propagacyjne</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<style>
:root{color-scheme:dark;--bg:#070d12;--panel:#101a23;--line:#29404f;--text:#eef5f8;--muted:#a9bac5;--green:#22c55e;--yellow:#facc15}
*{box-sizing:border-box}html,body{margin:0;min-height:100%;background:var(--bg);color:var(--text);font-family:Inter,"Segoe UI",Arial,sans-serif}body{background:radial-gradient(ellipse at 50% -20%,#183040 0,transparent 48%)}
.shell{width:min(1900px,calc(100% - 28px));margin:auto;padding:14px 0 28px}
.header{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:18px 22px;background:#101a23;border:1px solid var(--line);border-radius:12px;box-shadow:0 12px 32px #0007}.brand{display:flex;align-items:center;gap:16px}.brandmark{font-weight:1000;font-size:22px;color:#07140b;background:var(--green);padding:10px 13px;border-radius:9px}.brandname{font-size:clamp(18px,2.2vw,27px);font-weight:1000;letter-spacing:.04em}.subtitle{margin-top:5px;color:#c5d2d9;font-size:clamp(11px,1.4vw,15px);font-weight:800;letter-spacing:.12em}.clock{text-align:right;font-size:12px;color:#b8c8d1;font-weight:800;white-space:nowrap}.clock strong{display:block;font-size:16px;color:var(--green);margin-top:5px}
.toolbar{display:flex;align-items:center;gap:10px;flex-wrap:wrap;padding:12px 14px;margin:12px 0;background:#101a23;border:1px solid var(--line);border-radius:10px}.state{display:flex;align-items:center;gap:9px;margin-right:auto;color:#d2e0e6;font-size:12px;font-weight:900}.dot{width:10px;height:10px;border-radius:50%;background:var(--green);box-shadow:0 0 0 5px #22c55e22}.btn,select{border:1px solid #385363;background:#192b37;color:#f1f7fa;padding:10px 13px;border-radius:7px;font-weight:900;font-size:12px;cursor:pointer}.btn:hover{border-color:var(--green);background:#203945}.btn.primary{background:#159447;border-color:#159447;color:#04140a}.btn.primary:hover{background:#20b85a}
.layout{display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:14px;align-items:stretch}.card{background:#101a23;border:1px solid var(--line);border-radius:12px;overflow:hidden;box-shadow:0 10px 28px #0005;min-width:0}.cardhead{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:13px 17px;background:#152530;border-bottom:1px solid var(--line)}.cardhead h2{font-size:14px;letter-spacing:.06em;text-transform:uppercase;margin:0}.tag{font-size:10px;font-weight:1000;color:#7ff0a3;background:#123523;border:1px solid #256644;border-radius:30px;padding:6px 10px;white-space:nowrap}#map{height:670px;width:100%;background:#14232c;position:relative;overflow:hidden}#googleMap{position:absolute;inset:0;z-index:600;display:none}.mapwrap{position:relative;overflow:hidden}#map.google-active .leaflet-control-container{display:none}.mapwrap{position:relative}.map-credit{position:absolute;top:14px;right:14px;z-index:700;padding:8px 12px;border:1px solid #7dd3fc88;border-radius:7px;background:#07131ddc;color:#b8d8e6;font-size:10px;font-weight:900;letter-spacing:.12em;box-shadow:0 5px 18px #0008;pointer-events:none;backdrop-filter:blur(8px)}.map-credit b{color:#e9faff;font-size:11px;letter-spacing:.06em}.radius-ring{filter:drop-shadow(0 0 5px rgba(103,232,249,.42))}.mapfoot{display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;padding:12px 15px;border-top:1px solid var(--line);color:var(--muted);font-size:11px}.mapfoot strong{color:#eef5f8}
.side{display:flex;flex-direction:column;gap:12px}.sidecard{background:#101a23;border:1px solid var(--line);border-radius:11px;padding:16px}.sidecard h3{font-size:13px;margin:0 0 13px;letter-spacing:.06em}.selected{font-size:22px;font-weight:1000;color:#fff}.coords{margin-top:7px;font-size:11px;color:var(--muted);line-height:1.5}.metric{display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-top:1px solid #273b47;color:#b9c8d0;font-size:12px}.metric strong{color:#fff;font-size:15px}.radiusbadge{display:inline-flex;align-items:center;gap:8px;padding:8px 10px;border-radius:7px;background:#102d39;border:1px solid #28718a;color:#b9efff;font-size:12px;font-weight:1000}.citylist{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px}.citychip{font-size:11px;color:#e0eaf0;background:#1c2c36;border:1px solid #344b58;padding:6px 8px;border-radius:6px}.instructions{font-size:11px;color:var(--muted);line-height:1.6}.legend{margin-top:14px;padding:16px 18px;background:#101a23;border:1px solid var(--line);border-radius:11px}.legendhead{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:13px}.legendhead h2{font-size:14px;letter-spacing:.06em;margin:0;text-transform:uppercase}.legendgrid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}.legenditem{display:flex;align-items:center;gap:12px;min-height:72px;padding:13px;border:1px solid #314753;background:#172731;border-radius:8px}.swatch{width:20px;height:20px;flex:0 0 20px;border-radius:6px;box-shadow:0 0 12px currentColor33}.legendname{font-size:13px;font-weight:1000;color:#f5f8fa}.legenddesc{display:block;margin-top:4px;font-size:11px;color:#afc0c9;line-height:1.3}.notice{margin-top:12px;font-size:11px;line-height:1.55;color:#aebec8;background:#151f27;border-left:3px solid var(--yellow);border-radius:5px;padding:11px 13px}.forecast{margin-top:14px}.forecast iframe{width:100%;height:720px;border:0;display:block;background:#07111a}.forecastnote{font-size:11px;line-height:1.55;color:#b8c7cf;padding:12px 15px;background:#101a23;border-top:1px solid var(--line)}.city-label{background:#07121bd9;border:1px solid #e4edf2;color:#fff;border-radius:5px;padding:3px 6px;font-weight:900;font-size:12px;white-space:nowrap;box-shadow:0 2px 8px #000b}.city-dot{width:8px;height:8px;background:#fff;border:2px solid #07121b;border-radius:50%;box-shadow:0 0 0 2px #fff8}.leaflet-control-attribution{font-size:9px!important}.footer{text-align:center;color:#718490;font-size:10px;padding:17px 0 0}
@media(max-width:1100px){.layout{grid-template-columns:1fr}.side{display:grid;grid-template-columns:1fr 1fr}#map{height:590px}.legendgrid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:650px){.shell{width:calc(100% - 14px);padding-top:7px}.header{padding:12px;align-items:flex-start}.brand{gap:9px}.brandmark{font-size:17px;padding:9px}.clock{font-size:9px}.clock strong{font-size:12px}.toolbar .state{width:100%;padding:2px 0 6px}.btn,select{flex:1;padding:10px 7px;font-size:10px}.side{display:flex}#map{height:510px}.legendgrid{grid-template-columns:1fr}.legenditem{padding:10px;gap:9px;min-height:60px}.legendname{font-size:12px}.forecast iframe{height:650px}}
</style></head>
<body><div class="shell">
<header class="header"><div class="brand"><div class="brandmark">446</div><div><div class="brandname">446dx.pl</div><div class="subtitle">AKTUALNE WARUNKI PROPAGACYJNE</div></div></div><div class="clock"><span id="date">Ładowanie daty…</span><strong id="time">--:-- · CZAS POLSKI</strong></div></header>
<div class="toolbar"><div class="state"><span class="dot"></span><span id="status">Mapa Polski · kliknij wybrane miejsce, aby wyznaczyć obszar</span></div><button class="btn primary" id="gps">⌖ MOJA LOKALIZACJA</button><button class="btn" id="whole">CAŁA POLSKA</button><label for="radius" style="font-size:11px;font-weight:900;color:#c9d7de">PROMIEŃ</label><select id="radius"><option>50</option><option>100</option><option>150</option><option selected>200</option><option>250</option><option>300</option></select><span style="font-size:11px;color:#b7c7d0;font-weight:800">km</span></div>
<main class="layout"><section class="card"><div class="cardhead"><h2>Interaktywna mapa satelitarna Polski</h2><span class="tag">KLIKNIJ DOWOLNY PUNKT</span></div><div class="mapwrap"><div id="map"><div id="googleMap"></div></div><div class="map-credit">MAPA 446DX · <b>BY ML5403 / KALBAR</b></div></div><div class="mapfoot"><span><strong id="mapselection">Nie wybrano obszaru</strong></span><span>Przybliżaj kółkiem myszy · przeciągaj mapę · kliknij, aby wybrać środek promienia</span></div></section>
<aside class="side"><section class="sidecard"><h3>WYBRANY OBSZAR</h3><div class="selected" id="place">Kliknij mapę</div><div class="coords" id="coordstext">Wskaż np. Gdynię, Elbląg, Warszawę lub dowolne miejsce w Polsce.</div><div style="margin:13px 0"><span class="radiusbadge">◎ OBSZAR ANALIZY · <span id="radiuslabel">200</span> KM</span></div><div class="metric"><span>Powierzchnia koła</span><strong id="area">125 664 km²</strong></div><div class="metric"><span>Środek analizy</span><strong id="centerlabel">—</strong></div><h3 style="margin:16px 0 7px">MIASTA W ZASIĘGU</h3><div class="citylist" id="nearby"><span class="citychip">Wybierz punkt na mapie</span></div></section>
</aside></main>
<section class="legend"><div class="legendhead"><h2>Legenda warunków propagacyjnych</h2><span class="tag">POTENCJAŁ MODELOWY VHF / UHF</span></div><div class="legendgrid">
<div class="legenditem"><i class="swatch" style="background:#64748b"></i><div><span class="legendname">Brak / poniżej progu</span><span class="legenddesc">Brak istotnego sygnału modelowego</span></div></div>
<div class="legenditem"><i class="swatch" style="background:#4774df"></i><div><span class="legendname">Słaby</span><span class="legenddesc">Marginalne polepszenie warunków</span></div></div>
<div class="legenditem"><i class="swatch" style="background:#26b6cb"></i><div><span class="legendname">Umiarkowany</span><span class="legenddesc">Możliwe lepsze łączności lokalne</span></div></div>
<div class="legenditem"><i class="swatch" style="background:#38b66b"></i><div><span class="legendname">Dobry</span><span class="legenddesc">Podwyższony potencjał propagacji</span></div></div>
<div class="legenditem"><i class="swatch" style="background:#f0c632"></i><div><span class="legendname">Silny</span><span class="legenddesc">Sprzyjające dalekim łącznościom</span></div></div>
<div class="legenditem"><i class="swatch" style="background:#f27632"></i><div><span class="legendname">Bardzo silny</span><span class="legenddesc">Silne lub wyjątkowe polepszenie</span></div></div></div><div class="notice">Kolory i klasy opisują potencjał atmosferyczny modelowany przez Tropocast. To nie są pomiary z odbiorników ani gwarancja łączności. Rzeczywisty wynik zależy m.in. od wysokości i charakterystyki anten, mocy, polaryzacji oraz ukształtowania terenu.</div></section>
<section class="card forecast"><div class="cardhead"><h2>Szczegółowa prognoza obszarowa · Tropocast</h2><span class="tag">DO 72 GODZIN</span></div><iframe id="tropoFrame" src="https://tropocast.eu/map?lang=pl" title="Interaktywna prognoza Tropocast" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe></section>
<div class="footer">446dx.pl · Dane prognozy: Tropocast · Mapa satelitarna: Esri · Etykiety i dane geograficzne OpenStreetMap</div></div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
(()=>{const cities=[['Szczecin',53.4285,14.5528],['Koszalin',54.1944,16.1722],['Słupsk',54.4641,17.0287],['Gdańsk',54.3520,18.6466],['Gdynia',54.5189,18.5305],['Elbląg',54.1522,19.4040],['Olsztyn',53.7784,20.4801],['Białystok',53.1325,23.1688],['Bydgoszcz',53.1235,18.0084],['Toruń',53.0138,18.5984],['Poznań',52.4064,16.9252],['Gorzów Wlkp.',52.7368,15.2288],['Zielona Góra',51.9356,15.5062],['Warszawa',52.2297,21.0122],['Płock',52.5468,19.7064],['Łódź',51.7592,19.4550],['Lublin',51.2465,22.5684],['Wrocław',51.1079,17.0385],['Opole',50.6751,17.9213],['Katowice',50.2649,19.0238],['Kielce',50.8661,20.6286],['Kraków',50.0647,19.9450],['Rzeszów',50.0412,21.9991],['Częstochowa',50.8118,19.1203],['Radom',51.4027,21.1471],['Zakopane',49.2992,19.9496]];
const bounds=L.latLngBounds([[49,14],[54.9,24.3]]),map=L.map('map',{scrollWheelZoom:true}).setView([52.05,19.25],6);
const sat=L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',{maxZoom:19,attribution:'Tiles © Esri'}).addTo(map);
const labels=L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}',{maxZoom:19}).addTo(map);
const roads=L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Transportation/MapServer/tile/{z}/{y}/{x}',{maxZoom:19,opacity:.8}).addTo(map);
L.control.layers({'Satelita':sat,'Mapa drogowa':L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19})},{'Nazwy miejscowości i granice':labels,'Drogi':roads},{collapsed:true}).addTo(map);L.control.scale({metric:true,imperial:false}).addTo(map);
const layer=L.layerGroup().addTo(map);cities.forEach(([n,lat,lon])=>{const major=['Warszawa','Kraków','Wrocław','Poznań','Gdańsk','Gdynia','Szczecin','Łódź','Katowice','Lublin','Białystok','Olsztyn'].includes(n);const m=L.marker([lat,lon],{icon:L.divIcon({className:'',html:'<span class="city-dot" style="display:block"></span>',iconSize:[9,9],iconAnchor:[4,4]})}).addTo(layer);if(major)m.bindTooltip(n,{permanent:true,direction:'right',offset:[5,-2],className:'city-label'});else m.bindTooltip(n,{direction:'top'});});
let selected=null,ring=null,marker=null,googleMap=null,googleRing=null,googleMarker=null;const $=id=>document.getElementById(id),radius=()=>Number($('radius').value),rad=v=>v*Math.PI/180;
function distance(a,b){const R=6371,d1=rad(b[0]-a[0]),d2=rad(b[1]-a[1]),x=Math.sin(d1/2)**2+Math.cos(rad(a[0]))*Math.cos(rad(b[0]))*Math.sin(d2/2)**2;return 2*R*Math.asin(Math.sqrt(x));}
function nearest(lat,lon){let best='Wybrany punkt',dmin=1e9;cities.forEach(c=>{const d=distance([lat,lon],[c[1],c[2]]);if(d<dmin){dmin=d;best=c[0];}});return dmin<30?best:'Wybrany punkt';}
function choose(lat,lon,zoom=true){
selected=[lat,lon];const r=radius(),name=nearest(lat,lon);
if(ring)map.removeLayer(ring);if(marker)map.removeLayer(marker);
ring=L.circle(selected,{radius:r*1000,color:'#67e8f9',weight:2,opacity:.96,fillColor:'#67e8f9',fillOpacity:0,dashArray:'10 8',className:'radius-ring',interactive:false}).addTo(map);
marker=L.circleMarker(selected,{radius:7,color:'#e0f7ff',weight:2,fillColor:'#22d3ee',fillOpacity:.95}).addTo(map).bindPopup('<b>'+name+'</b><br>Promień analizy: '+r+' km<br>'+lat.toFixed(4)+', '+lon.toFixed(4)).openPopup();
if(zoom)map.fitBounds(ring.getBounds(),{padding:[72,72],maxZoom:8});
if(googleMap){
 if(googleRing)googleRing.setMap(null);if(googleMarker)googleMarker.setMap(null);
 googleRing=new google.maps.Circle({map:googleMap,center:{lat:lat,lng:lon},radius:r*1000,strokeColor:'#67e8f9',strokeOpacity:.96,strokeWeight:2,fillColor:'#67e8f9',fillOpacity:0,clickable:false,zIndex:10});
 googleMarker=new google.maps.Marker({map:googleMap,position:{lat:lat,lng:lon},title:name,icon:{path:google.maps.SymbolPath.CIRCLE,scale:7,fillColor:'#22d3ee',fillOpacity:1,strokeColor:'#e0f7ff',strokeWeight:2},zIndex:100});
 if(zoom)googleMap.fitBounds(googleRing.getBounds(),72);
}
$('place').textContent=name;$('coordstext').textContent=lat.toFixed(5)+'°N · '+lon.toFixed(5)+'°E';$('radiuslabel').textContent=r;$('area').textContent=Math.round(Math.PI*r*r).toLocaleString('pl-PL')+' km²';$('centerlabel').textContent=lat.toFixed(2)+' / '+lon.toFixed(2);$('mapselection').textContent=name+' · promień '+r+' km';$('status').textContent='Wybrano obszar analizy · '+name;
const near=cities.map(c=>({name:c[0],d:distance(selected,[c[1],c[2]])})).filter(c=>c.d<=r).sort((a,b)=>a.d-b.d).slice(0,12);
$('nearby').innerHTML=near.map(c=>'<span class="citychip">'+c.name+' · '+Math.round(c.d)+' km</span>').join('')||'<span class="citychip">Brak dużych miast w promieniu</span>';
}
map.on('click',e=>choose(e.latlng.lat,e.latlng.lng));$('radius').addEventListener('change',()=>{if(selected)choose(selected[0],selected[1],false);});$('whole').addEventListener('click',()=>{map.fitBounds(bounds,{padding:[12,12]});$('status').textContent='Widok całej Polski';});$('gps').addEventListener('click',()=>{if(!navigator.geolocation){$('status').textContent='Geolokalizacja niedostępna';return;}$('status').textContent='Oczekiwanie na zgodę lokalizacji…';navigator.geolocation.getCurrentPosition(p=>{choose(p.coords.latitude,p.coords.longitude);$('status').textContent='Wykryto lokalizację GPS · obszar '+radius()+' km';},e=>{$('status').textContent=e.code===1?'Brak zgody na lokalizację':'Nie udało się wykryć lokalizacji';},{enableHighAccuracy:true,timeout:15000,maximumAge:120000});});

function initGoogleSatellite(){
 const target=$('googleMap');target.style.display='block';$('map').classList.add('google-active');
 googleMap=new google.maps.Map(target,{center:{lat:52.05,lng:19.25},zoom:6,mapTypeId:'satellite',mapTypeControl:true,mapTypeControlOptions:{mapTypeIds:['satellite','hybrid','roadmap','terrain']},streetViewControl:false,fullscreenControl:true,rotateControl:false,clickableIcons:false,gestureHandling:'greedy'});
 cities.forEach(([name,lat,lon])=>{
  const major=['Warszawa','Kraków','Wrocław','Poznań','Gdańsk','Gdynia','Szczecin','Łódź','Katowice','Lublin','Białystok','Olsztyn'].includes(name);
  const m=new google.maps.Marker({map:googleMap,position:{lat:lat,lng:lon},title:name,icon:{path:google.maps.SymbolPath.CIRCLE,scale:major?4.5:3.2,fillColor:'#ffffff',fillOpacity:1,strokeColor:'#07121b',strokeWeight:2},label:major?{text:name,color:'#ffffff',fontSize:'12px',fontWeight:'800'}:null});
  m.addListener('click',()=>choose(lat,lon,true));
 });
 googleMap.addListener('click',e=>choose(e.latLng.lat(),e.latLng.lng(),true));
 googleMap.fitBounds({north:54.9,south:49.0,east:24.3,west:14.0});
 $('status').textContent='Google Satellite · kliknij miejsce, aby wyznaczyć obszar';
}
function clock(){const n=new Date();$('date').textContent=new Intl.DateTimeFormat('pl-PL',{timeZone:'Europe/Warsaw',weekday:'long',day:'2-digit',month:'long',year:'numeric'}).format(n).toUpperCase();$('time').textContent=new Intl.DateTimeFormat('pl-PL',{timeZone:'Europe/Warsaw',hour:'2-digit',minute:'2-digit',second:'2-digit'}).format(n)+' · CZAS POLSKI';}clock();setInterval(clock,1000);map.fitBounds(bounds,{padding:[14,14]});
if(<?= json_encode($googleMapsApiKey !== '') ?>){
 window.initGoogleSatellite=initGoogleSatellite;
 const googleScript=document.createElement('script');
 googleScript.src='https://maps.googleapis.com/maps/api/js?key='+encodeURIComponent(<?= json_encode($googleMapsApiKey, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)+'&callback=initGoogleSatellite&v=weekly';
 googleScript.async=true;googleScript.defer=true;
 googleScript.onerror=()=>{$('status').textContent='Błąd Google Maps API — sprawdź klucz, ograniczenia domeny i billing';};
 document.head.appendChild(googleScript);
}else{
 $('status').textContent='Mapa satelitarna Esri · dodaj klucz Google Maps, aby włączyć Google Satellite';
}

})();
</script></body></html>