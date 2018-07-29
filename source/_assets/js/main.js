window.Vue = require('vue');

Vue.component('table-of-contents', require('./components/TableOfContents.vue'));

const app = new Vue({
  el: '#root'
});