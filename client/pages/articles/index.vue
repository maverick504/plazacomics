<template>
  <div>
    <div class="hero bg-primary text-light" style="padding: 0px;">
      <div class="container">
        <div class="hero-body" style="padding: 60px 0px;">
          <h1 style="display: block; margin: 0px;">
            <span class="bg-primary">{{ article.title }}</span>
          </h1>
          <p style="display: block; margin: 0px;">
            <span class="bg-primary">{{ article.subtitle }}</span>
          </p>
        </div>
      </div>
    </div>
    <div class="container my-xl">
      <div class="columns">
        <div class="column col-3 col-md-12 mb-xl">
          <ul class="nav">
            <li v-for="(section, index) in article.sections" v-if="section.title" :key="index" class="nav-item">
              <nuxt-link :to="'#'+section.id">
                {{ section.title }}
              </nuxt-link>
            </li>
          </ul>
        </div>
        <div class="divider-vert hide-md" />
        <div class="column col-md-12">
          <section v-for="(section, index) in article.sections" :key="index">
            <h2 v-if="section.title" :id="section.id">{{ section.title }}</h2>
            <vue-showdown :markdown="section.content.join('\n')"/>
          </section>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  head () {
    return { title: this.article.title }
  },

  async asyncData ({ params, error }) {
    try {
      var article = await import(`~/data/articles/${params.article}.json`).then(m => m.default || m)

      return {
        article
      }
    } catch (err) {
      return error({ error: err })
    }
  },

  data: () => ({
    article: null
  })
}
</script>
