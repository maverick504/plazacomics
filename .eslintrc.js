module.exports = {
  root: true,
  parserOptions: {
    parser: 'babel-eslint',
    ecmaVersion: 2017,
    sourceType: 'module'
  },
  extends: [
    "plugin:vue/recommended",
    "@vue/standard"
  ],
  rules: {
    "vue/max-attributes-per-line": "off",
    "no-useless-escape": "off"
  }
}
