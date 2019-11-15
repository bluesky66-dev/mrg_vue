<template>
  <div class="row justify-between items-center">
    <div class="card-title">{{ title }}</div>
    <div>
      <q-btn
        v-on:click="$emit('action')"
        flat
        :disable="disable"
        color="primary">
        <slot></slot>
      </q-btn>
    </div>
  </div>
</template>
<script>
import {
  QBtn,
} from 'quasar-framework';
export default {
  components: {
    QBtn,
  },
  props: {
    title: {
      required: true,
    },
    disable: {
      type: Boolean,
      default: false,
    }
  },
};
</script>

<style lang="scss" scoped>
  .card-title {
    font-size: 1.35rem;
    letter-spacing: 1.45px;
    text-transform: uppercase;
    font-weight: 500;
  }
</style>
