<template>
  <div>
    <div class="hero bg-primary text-light" style="padding: 0px;">
      <div class="container">
        <div class="hero-body" style="padding: 60px 0px;">
          <h1  style="display: block; margin: 0px;">
            <span class="bg-primary">Preguntas Frecuentes</span>
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
              <nuxt-link to="#que_es_plazacomics">¿Qué es PlazaComics?</nuxt-link>
            </li>
            <li class="nav-item">
              <nuxt-link to="#plazacomics_se_queda_con_los_derechos_de_mi_obra">¿PlazaComics se queda con los derechos de mi obra?</nuxt-link>
            </li>
            <li class="nav-item">
              <nuxt-link to="#puedo_monetizar_mi_obra">¿Puedo monetizar mi obra?</nuxt-link>
            </li>
            <li class="nav-item">
              <nuxt-link to="#por_que_publicar_mi_obra_aqui">¿Por qué publicar mi obra aquí?</nuxt-link>
            </li>
            <li class="nav-item">
              <nuxt-link to="#como_sobrevive_plazacomics">¿Cómo sobrevive PlazaComics?</nuxt-link>
            </li>
            <li class="nav-item">
              <nuxt-link to="#como_puedo_apoyar_a_plazacomics">¿Cómo puedo apoyar a PlazaComics?</nuxt-link>
            </li>
          </ul>
        </div>
        <div class="divider-vert hide-md"></div>
        <div class="column col-md-12">
          <h2 id="que_es_plazacomics">¿Qué es PlazaComics?</h2>
          <p>PlazaComics es una plataforma donde dibujantes de cómics pueden, de forma totalmente gratuita subir sus obras y darlas a conocer al mundo. Ponemos mucho esfuerzo para que la plataforma sea súper intuitiva tanto para creadores de contenido como para lectores.</p>
          <h2 id="plazacomics_se_queda_con_los_derechos_de_mi_obra">¿PlazaComics se queda con los derechos de mi obra?</h2>
          <p>Claro que no! No nos quedamos con los derechos de tu obra, los derechos siguen siendo tuyos y eres libre de publicar tu obra en otros sitios web.</p>
          <h2 id="puedo_monetizar_mi_obra">¿Puedo monetizar mi obra?</h2>
          <p>No directamente en la plataforma(por ahora), sin embargo, eres libre de anexar links a una cuenta en Patreon, página web, página de Facebook o cualquier otra red social de tu obra.</p>
          <h2 id="por_que_publicar_mi_obra_aqui">¿Por qué publicar mi obra aquí?</h2>
          <p>PlazaComics es una plataforma diseñada para que la lectura de cómics sea una experiencia agradable para los lectores, no contiene anuncios y tiene una interfáz amigable para los usuarios. PlazaComics cuenta también con sistemas que dificultan crawlear, es decir, robar imágenes de las páginas de tus cómics para resubirlos en otros sitios.</p>
          <h2 id="como_sobrevive_plazacomics">¿Cómo sobrevive PlazaComics?</h2>
          <p>De momento PlazaComics no genera ingresos, los costos del servidor, marketing y demás los paga el fundador de la plataforma. PlazaComics está diseñado para implementar en el futuro un sistema que permita a PlazaComics como tal y a sus autores generar ingresos.</p>
          <h2 id="como_puedo_apoyar_a_plazacomics">¿Cómo puedo apoyar a PlazaComics?</h2>
          <p>Déjanos saber que aspectos de la plataforma podemos mejorar, tanto si eres lector como creador de contenido, también nos puedes ayudar siguiéndonos en las redes sociales y contándole a tus amigos!</p>
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
