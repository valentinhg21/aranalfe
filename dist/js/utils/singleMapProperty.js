const createMap = (container, lat, lng) => {
   const POINT_IMAGE = `${ajax_var.image}/point-map.png`;
  const map = L.map(container, { scrollWheelZoom: true }).setView([lat, lng], 14);
  L.tileLayer(
    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
    {
      tileSize: 512,
      zoomOffset: -1,
      minZoom: 5,
      maxZoom: 20,
      crossOrigin: true,
      scrollwheel: true,
    }
  ).addTo(map);

  const propertyICON = L.icon({
    iconUrl: POINT_IMAGE,
    iconSize: [30, 30],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32],
  });

  L.marker([lat, lng], { icon: propertyICON }).addTo(map);

  return map;
};

export const singleMapProperty = () => {
  const mapSingle = document.getElementById("mapviewproperty");
  const mapHero = document.getElementById("mapSingleHero");
 

  if (mapSingle) {
    createMap(mapSingle, mapSingle.dataset.lat, mapSingle.dataset.long);
  }

  if (mapHero) {
    createMap(mapHero, mapHero.dataset.lat, mapHero.dataset.long);
  }
};