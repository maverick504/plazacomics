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
const DashboardSeries = () => import('~/pages/dashboard/series').then(m => m.default || m)
const DashboardPosts = () => import('~/pages/dashboard/posts').then(m => m.default || m)

const Library = () => import('~/pages/library/index').then(m => m.default || m)
const Notifications = () => import('~/pages/notifications/index').then(m => m.default || m)

const Feed = () => import('~/pages/feed/index').then(m => m.default || m)
const CreatePostIllustration = () => import('~/pages/posts/create/illustration').then(m => m.default || m)
const EditPost = () => import('~/pages/posts/edit').then(m => m.default || m)
const ShowPost = () => import('~/pages/posts/show').then(m => m.default || m)

const IllustrationsIndex = () => import('~/pages/illustrations/index').then(m => m.default || m)

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
const ShowAuthor = () => import('~/pages/authors/show/index').then(m => m.default || m)
const ShowAuthorSeries = () => import('~/pages/authors/show/series').then(m => m.default || m)
const ShowAuthorIllustrations = () => import('~/pages/authors/show/illustrations').then(m => m.default || m)

const ScheduleIndex = () => import('~/pages/schedule/index').then(m => m.default || m)

const Article = () => import('~/pages/articles/index').then(m => m.default || m)

const PublishingLanding = () => import('~/pages/other/publishing').then(m => m.default || m)
const CommunityLanding = () => import('~/pages/other/community').then(m => m.default || m)

const routes = [
  { path: '/', name: 'home', component: Home },

  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },
  { path: '/password/reset', name: 'password.request', component: PasswordRequest },
  { path: '/password/reset/:token', name: 'password.reset', component: PasswordReset },

  { path: '/settings',
    component: Settings,
    children: [
      { path: 'profile', name: 'settings.profile', component: SettingsProfile },
      { path: 'password', name: 'settings.password', component: SettingsPassword }
    ] },

  { path: '/dashboard',
    component: Dashboard,
    children: [
      { path: 'series', name: 'dashboard', component: DashboardSeries },
      { path: 'posts', name: 'dashboard.posts', component: DashboardPosts }
    ] },

  { path: '/library', name: 'library.index', component: Library },
  { path: '/library/page/:page', name: 'library.page', component: Library },
  { path: '/notifications/:filter', name: 'notifications', component: Notifications },

  { path: '/feed', name: 'feed', component: Feed },
  { path: '/posts/create/illustration', name: 'posts.create.illustration', component: CreatePostIllustration },
  { path: '/posts/:id/edit', name: 'posts.edit', component: EditPost },
  { path: '/posts/:id', name: 'posts.show', component: ShowPost },

  { path: '/illustrations', name: 'illustrations.index', component: IllustrationsIndex },

  { path: '/series', name: 'series.index', component: SeriesIndex },
  { path: '/series/create', name: 'series.create', component: CreateSerie },
  { path: '/series/:id/edit',
    component: EditSerie,
    children: [
      { path: 'details', name: 'series.edit.details', component: EditSerieDetails },
      { path: 'chapters', name: 'series.edit.chapters', component: EditSerieChapters }
    ] },
  { path: '/series/:id/:slug', name: 'series.show', component: ShowSerie },

  { path: '/series/:serieId/chapters/create', name: 'chapters.create', component: CreateChapter },
  { path: '/chapters/:chapterId/edit', name: 'chapters.edit', component: EditChapter },
  { path: '/series/:serieSlug/chapters/:chapterId/:chapterSlug', name: 'chapters.show', component: ShowChapter },

  { path: '/authors', name: 'authors.index', component: AuthorsIndex },
  { path: '/authors/:id/:username',
    component: ShowAuthor,
    children: [
      { path: '', name: 'authors.show', component: ShowAuthorSeries },
      { path: 'illustrations', name: 'authors.show.illustrations', component: ShowAuthorIllustrations }
    ] },

  { path: '/schedule', name: 'schedule.index', component: ScheduleIndex },

  { path: '/legal/:article', name: 'legal', component: Article },
  { path: '/help/:article', name: 'help', component: Article },

  { path: '/publishing', name: 'landing.publishing', component: PublishingLanding },
  { path: '/community', name: 'landing.community', component: CommunityLanding }
]

export function createRouter () {
  return new Router({
    routes,
    scrollBehavior,
    mode: 'history'
  })
}
