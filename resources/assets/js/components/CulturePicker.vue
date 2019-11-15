<template>
    <div class="justify-start self-end culture-picker">
        <q-field>
            <q-select class=""
                v-model="culture"
                :options="cultureOptions" />
        </q-field>
    </div>
</template>

<style lang="scss" scoped>
.culture-picker {
  margin: 0 10px;
  width: 300px;
  display: inline-block;
}
</style>

<script>
import {
  QSelect,
  QField,
} from 'quasar-framework';

import get from 'lodash/get';
import find from 'lodash/find';
import axios from 'axios';

export default {
  name:'momentum-culture-picker',
  components: {
    QSelect,
    QField
  },
  data() {
    return {
      cultures:window.cultures
    };
  },
  watch: {
  },
  computed: {
    culture:{
      get:function () {
        return this.$i18n.locale;
      },
      set:function (val) {
        const code  = get(find(this.cultures,{code:val}),'id',1);
        axios
          .post(`/set-culture/${code}`)
          .then(()=>{});
        this.$i18n.locale = val;
      }
    },
    cultureOptions() {
      if (!this.cultures) {
        return [];
      } else {
        return this.cultures.map(culture => ({
          value: culture.code,
          label: culture.name_key_translated
        }));
      }

    }
  },
  mounted() {
    //console.log('Component culture picker.');
  }
};
</script>
