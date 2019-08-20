<template>
  <div>
    <router-view :key="$route.fullPath"/>
    <no-ssr>
      <cookie-law theme="blood-orange" button-text="Si, está bien!">
        <template slot="message">
          En este sitio usamos cookies 🍪 para brindarte una mejor experiencia en la plataforma.
        </template>
      </cookie-law>
    </no-ssr>
  </div>
</template>

<script>
export default {}
</script>
