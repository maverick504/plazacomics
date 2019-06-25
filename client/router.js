import Vue from 'vue'
import Router from 'vue-router'
import { scrollBehavior } from '~/utils'

Vue.use(Router)

const Home = () => import('~/pages/home/index').then(m => m.default || m)

const Login = () => import('~/pages/auth/login').then(m => m.default || m)
const Register = () => import('~/pages/auth/register').then(m => m.default || m)
const PasswordReset = () => import('~/pages/auth/password/reset').then(m => m.default || m)
const PasswordRequest = () => import('~/pages/auth/password/email').then(m => m.default || m)

const Settings = () => import('~/pages/settings/index').then(m => m.default || m)
const SettingsProfile = () => import('~/pages/settings/profile').then(m => m.default || m)
const SettingsPassword = () => import('~/pages/settings/password').then(m => m.default || m)

const Dashboard = () => import('~/pages/dashboard/index').then(m => m.default || m)

const Library = () => import('~/pages/library/index').then(m => m.default || m)

const SeriesIndex = () => import('~/pages/series/index').then(m => m.default || m)
const CreateSerie = () => import('~/pages/series/create').then(m => m.default || m)
const EditSerie = () => import('~/pages/series/edit/index').then(m => m.default || m)
const EditSerieDetails = () => import('~/pages/series/edit/details').then(m => m.default || m)
const EditSerieChapters = () => import('~/pages/series/edit/chapters').then(m => m.default || m)
const ShowSerie = () => import('~/pages/series/show').then(m => m.default || m)

const CreateChapter = () => import('~/pages/chapters/create').then(m => m.default || m)
const EditChapter = () => import('~/pages/chapters/edit').then(m => m.default || m)
const ShowChapter = () => import('~/pages/chapters/show').then(m => m.default || m)

const AuthorsIndex = () => import('~/pages/authors/index').then(m => m.default || m)
const ShowAuthor = () => import('~/pages/authors/show').then(m => m.default || m)

const InfoPublishing = () => import('~/pages/info/publishing').then(m => m.default || m)
const InfoPrivacyPolicy = () => import('~/pages/info/privacyPolicy').then(m => m.default || m)
const InfoFaq = () => import('~/pages/info/faq').then(m => m.default || m)

const routes = [
  { path: '/', name: 'home', component: Home },

  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },
  { path: '/password/reset', name: 'password.request', component: PasswordRequest },
  { path: '/password/reset/:token', name: 'password.reset', component: PasswordReset },

  { path: '/settings',
    component: Settings,
    children: [
      { path: '', redirect: { name: 'settings.profile' } },
      { path: 'profile', name: 'settings.profile', component: SettingsProfile },
      { path: 'password', name: 'settings.password', component: SettingsPassword }
    ] },

  { path: '/dashboard', name: 'dashboard', component: Dashboard },

  { path: '/library', name: 'library', component: Library },

  { path: '/series', name: 'series.index', component: SeriesIndex },
  { path: '/series/create', name: 'series.create', component: CreateSerie },
  { path: '/series/:id/edit',
    component: EditSerie,
    children: [
      { path: '/series/:id/edit', name: 'series.edit.details', component: EditSerieDetails },
      { path: '/series/:id/chapters', name: 'series.edit.chapters', component: EditSerieChapters }
    ] },
  { path: '/series/:id/:slug', name: 'series.show', component: ShowSerie },

  { path: '/series/:serieId/chapters/create', name: 'chapters.create', component: CreateChapter },
  { path: '/chapters/:chapterId/edit', name: 'chapters.edit', component: EditChapter },
  { path: '/series/:serieSlug/chapters/:chapterId/:chapterSlug', name: 'chapters.show', component: ShowChapter },

  { path: '/authors', name: 'authors.index', component: AuthorsIndex },
  { path: '/authors/:id/:username', name: 'authors.show', component: ShowAuthor },

  { path: '/publishing', name: 'info.publishing', component: InfoPublishing },
  { path: '/privacy-policy', name: 'info.privacyPolicy', component: InfoPrivacyPolicy },
  { path: '/faq', name: 'info.faq', component: InfoFaq }
]

export function createRouter () {
  return new Router({
    routes,
    scrollBehavior,
    mode: 'history'
  })
}
