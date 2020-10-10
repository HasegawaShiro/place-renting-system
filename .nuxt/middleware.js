const middleware = {}

middleware['auth'] = require('..\\resources\\nuxt\\middleware\\auth.js')
middleware['auth'] = middleware['auth'].default || middleware['auth']

middleware['csrf'] = require('..\\resources\\nuxt\\middleware\\csrf.js')
middleware['csrf'] = middleware['csrf'].default || middleware['csrf']

export default middleware
