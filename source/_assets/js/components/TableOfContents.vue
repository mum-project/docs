<template>
    <div class="max-w-full w-auto mr-6" v-if="links.length > 0">
        <div class="mb-2 w-auto max-w-full truncate" :class="link.isChild ? 'ml-4' : ''" v-for="link in links">
            <a class="text-grey-darker px-6 py-1 mb-1 text-sm no-underline hover:text-black focus:text-black truncate"
               :title="link.text"
               :href="link.href">{{ link.text }}</a>
        </div>
    </div>
</template>

<script>
  import _ from 'lodash';

  const AnchorJs = require('anchor-js');
  const anchors = new AnchorJs();

  export default {
    props: ['rows'],
    data() {
      return {
        links: []
      }
    },
    methods: {
      scrollTo(el) {
        const bounds = el.getBoundingClientRect();
        document.body.scrollBy(0, 200);
      },
      getHeadingText(element) {
        let text = '';
        element.childNodes.forEach(function (node) {
          if (node.textContent && node.textContent.length > 0) {
            text += node.textContent;
          }
        });
        return text;
      }
    },
    mounted() {
      anchors.options = {placement: 'left', class: 'text-grey-dark'};
      anchors.add();
      let getHeadingText = this.getHeadingText;
      this.links = anchors.elements.filter((el) => _.includes(['H2', 'H3'], el.tagName)).map((el) => {
        return {
          isChild: el.tagName === 'H3',
          text: getHeadingText(el),
          href: el.querySelector('.anchorjs-link').getAttribute('href'),
          el: el,
        };
      });
    }
  }
</script>