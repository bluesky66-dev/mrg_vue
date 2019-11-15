import { configure } from "@storybook/vue";

function loadStories() {
  require("../resources/assets/js/stories");
}

configure(loadStories, module);
