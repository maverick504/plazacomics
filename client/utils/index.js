/**
 * Get cookie from request.
 *
 * @param  {Object} req
 * @param  {String} key
 * @return {String|undefined}
 */
export function cookieFromRequest (req, key) {
  if (!req.headers.cookie) {
    return
  }

  const cookie = req.headers.cookie.split(';').find(
    c => c.trim().startsWith(`${key}=`)
  )

  if (cookie) {
    return cookie.split('=')[1]
  }
}

/**
 * https://router.vuejs.org/en/advanced/scroll-behavior.html
 */
export async function scrollBehavior (to, from, savedPosition) {
  if (savedPosition) {
    return savedPosition
  }

  // If two routes are in the same group, keep scroll.
  const groups = {
    'settings.profile': 1,
    'settings.password': 1,
    'dashboard': 2,
    'dashboard.posts': 2,
    'series.edit.details': 3,
    'series.edit.chapters': 3,
    'authors.show': 4,
    'authors.show.illustrations': 4,
    'schedule.index': 5
  }

  if ((groups[to.name] !== undefined && groups[from.name] !== undefined && groups[to.name] === groups[from.name])) {
    return false
  }

  return { x: 0, y: 0 }
}
