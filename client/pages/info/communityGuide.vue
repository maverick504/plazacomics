<template>
  <div>
    <div class="hero bg-primary text-light" style="padding: 0px;">
      <div class="container">
        <div class="hero-body" style="padding: 60px 0px;">
          <h1 style="display: block; margin: 0px;">
            <span class="bg-primary">Guia de Contenido y de la Comunidad</span>
          </h1>
          <p style="display: block; margin: 0px;">
            <span class="bg-primary">Algunas de las preguntas que nos han hecho los usuarios.</span>
          </p>
        </div>
      </div>
    </div>
    <div class="container my-xl">
      <div class="columns">
        <div class="column col-3 col-md-12 mb-xl">
          <ul class="nav">
            <li class="nav-item">
              <nuxt-link to="#respeto">
                Respeto
              </nuxt-link>
            </li>
            <li class="nav-item">
              <nuxt-link to="#contenido">
                Contenido
              </nuxt-link>
            </li>
            <li class="nav-item">
              <nuxt-link to="#ayudanos-a-mejorar">
                Ayúdanos a mejorar esta guía
              </nuxt-link>
            </li>
          </ul>
        </div>
        <div class="divider-vert hide-md" />
        <div class="column col-md-12">
          <p>Esta guia cubre el comportamiento apropiado que se espera de los usuarios de la plataforma y va dirigido a todos los usuarios, tanto a autores como lectores.</p>
          <h2 id="respeto">
            Respeto
          </h2>
          <p>Siempre sé respetuoso con los usuarios del la plataforma. No agredas o fomentes agresiones.</p>
          <h2 id="contenido">
            Contenido
          </h2>
          <p>
            No se aceptarán en la plataforma los siguientes tipos de contenido:
            <ul>
              <li>Material que infrinja derechos de autor. El material publicado debe ser todo de tu autoría o debes estar autorizado por el autor original para publicar dicho contenido.</li>
              <li>Material con exceso de violencia o sexo, por ejemplo gore extremo o hentai directamente. Si tu obra tiene contenido adulto, no olvides marcar la casilla "contenido explícito" antes de publicar.</li>
            </ul>
          </p>
          <h2 id="ayudanos-a-mejorar">
            Ayúdanos a mejorar esta guía
          </h2>
          <p>Esta guia apenas está en construcción e irá evolucionando con el tiempo. Si discrepas con el contenido de la misma o quieres sugerir algo, no dudes en contactarnos.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  head () {
    return { title: 'Preguntas Frecuentes' }
  }
}
</script>
