<template>
  <div class="mb-4">
    <label class="block mb-1 font-semibold">Group Area (Draw on Map)</label>
    <div ref="mapRef" style="height: 300px; border-radius: 8px; overflow: hidden;"></div>
    <div v-if="form.errors.geometry" class="text-red-500 text-sm mt-1">{{ form.errors.geometry }}</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// Import Leaflet and CSS
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet-draw/dist/leaflet.draw.css';
import 'leaflet-draw';

const props = defineProps({
  group: Object,
});

const form = useForm({
  name: props.group?.name || '',
  geometry: props.group?.geometry || null,
});

const mapRef = ref(null);
const drawnLayer = ref(null);

onMounted(() => {
  const map = L.map(mapRef.value).setView([33.0, 10.5], 8);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
  }).addTo(map);

  // Draw control
  const drawControl = new L.Control.Draw({
    draw: {
      marker: false,
      polyline: false,
      circle: false,
      rectangle: true,
      polygon: true,
    },
    edit: {
      featureGroup: new L.FeatureGroup().addTo(map),
    },
  });
  map.addControl(drawControl);

  // If editing, show existing geometry
  if (form.geometry) {
    const geo = L.geoJSON(form.geometry).addTo(map);
    map.fitBounds(geo.getBounds());
    drawnLayer.value = geo;
  }

  map.on(L.Draw.Event.CREATED, function (e) {
    if (drawnLayer.value) {
      map.removeLayer(drawnLayer.value);
    }
    drawnLayer.value = e.layer;
    e.layer.addTo(map);
    form.geometry = e.layer.toGeoJSON().geometry;
  });
  map.on(L.Draw.Event.EDITED, function (e) {
    if (e.layers && e.layers.getLayers().length > 0) {
      form.geometry = e.layers.getLayers()[0].toGeoJSON().geometry;
    }
  });
  map.on(L.Draw.Event.DELETED, function () {
    form.geometry = null;
    if (drawnLayer.value) {
      map.removeLayer(drawnLayer.value);
      drawnLayer.value = null;
    }
  });
});
</script> 