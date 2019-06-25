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

  const findEl = async (hash, x) => {
    return document.querySelector(hash) ||
      new Promise((resolve, reject) => {
        if (x > 50) {
          return resolve()
        }
        setTimeout(() => { resolve(findEl(hash, ++x || 1)) }, 100)
      })
  }

  if (to.hash) {
    let el = await findEl(to.hash)
    const navbarHeight = 72
    const padding = 24
    if ('scrollBehavior' in document.documentElement.style) {
      return window.scrollTo({ top: el.offsetTop - navbarHeight - padding, behavior: 'smooth' })
    } else {
      return window.scrollTo(0, el.offsetTop - navbarHeight - padding)
    }
  }

  return { x: 0, y: 0 }
}
