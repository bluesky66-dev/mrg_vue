import { storiesOf } from '@storybook/vue';
import { linkTo } from '@storybook/addon-links';

import MyButton from './MyButton';
import Welcome from './Welcome';

import Login from '../components/Login.vue';

storiesOf('1. Login', module).add('1.1 ', () => ({
  components: { Login },
  template: '<login />',
  methods: { action: linkTo('Button') }
}));

storiesOf('Welcome', module).add('to Storybook', () => ({
  components: { Welcome },
  template: '<welcome :showApp="action" />',
  methods: { action: linkTo('Button') }
}));

storiesOf('Button', module)
  .add('with text', () => ({
    components: { MyButton },
    template: '<my-button @click="action">Hello Button</my-button>',
    methods: { action: linkTo('clicked') }
  }))
  .add('with some emoji', () => ({
    components: { MyButton },
    template: '<my-button @click="action">😀 😎 👍 💯</my-button>',
    methods: { action: linkTo('clicked') }
  }));
