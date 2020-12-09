const middleware = {}

middleware['admin'] = require('..\\resources\\nuxt\\middleware\\admin.js')
middleware['admin'] = middleware['admin'].default || middleware['admin']

middleware['auth'] = require('..\\resources\\nuxt\\middleware\\auth.js')
middleware['auth'] = middleware['auth'].default || middleware['auth']

middleware['csrf'] = require('..\\resources\\nuxt\\middleware\\csrf.js')
middleware['csrf'] = middleware['csrf'].default || middleware['csrf']

export default middleware
