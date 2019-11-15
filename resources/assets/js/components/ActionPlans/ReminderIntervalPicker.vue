<template>
  <div class="reminder-interval-picker col">
    <div v-if="frequency === 'never'"
        class="text-primary">
      {{ $t('global.datepicker.set_frequency.label') }}
    </div>
    <div v-if="frequency !== 'never'"
        class="text-primary">
      {{ $t('global.datepicker.set_frequency.from_date_label') }}
    </div>
    <div class="row">
      <q-field class="col" 
        :style="{marginTop:0, marginRight: '10px'}">
        <q-select v-model="frequency"
            :options="frequencyOptions"></q-select>
      </q-field>
      <q-field class="col" 
          :style="{marginTop:0}"
          v-if="frequency !== 'never'"
          :error="dateError"
          :error-label="dateErrorLabel">
        <q-datetime no-clear
            :placeholder="datePlaceholder"
            format="MMM D, YYYY"
            v-model="starts_at"
            :min="minDate"
            :max="dateMaxDate || maxDate"
            v-on:change="$emit('change')"></q-datetime>
      </q-field>
    </div>
  </div>
</template>
<script>
import {
  QField,
  QSelect,
  QDatetime,
  date
} from 'quasar-framework';
const today = new Date();
const { addToDate } = date;

export default {
  //
  name: 'ReminderIntervalPicker',
  components: {
    QField,
    QSelect,
    QDatetime,
  },
  props: [
    'id',
    'value',
    'disabled',
    'date-placeholder',
    'date-error',
    'date-error-label',
    'date-min-date',
    'date-max-date'
  ],

  data() {
    return {
      today: addToDate((new Date()), { days: 1 }),
      // minDate: addToDate(today, { days: 1 }),
      minDate: this.getMinDate(),
      maxDate: addToDate(today, { days: 365 })
    };
  },
  computed: {
    frequency: {
      set(frequency) {
        this.$emit('reset');

        if (!this.value.starts_at) {
          this.value.starts_at = null;
        }
        if (frequency === 'never') {
          this.value.starts_at = null;
        }

        this.enabled = true;
        this.value.id = this.id;

        this.value.frequency = frequency;

        return this.value.frequency;
      },
      get() {
        //console.log('ReminderIntervalPicker frequency get', this.value.frequency);
        if (this.value && this.value.frequency) {
          return this.value.frequency;
        } else {
          return 'never';
        }
      }
    },
    starts_at: {
      set(starts_at) {
        this.$emit('reset');
        if (starts_at === null || starts_at === undefined) {
          this.value.frequency = 'never';
        }
        this.enabled = true;
        this.value.id = this.id;
        //this.$emit('change')
        return this.value.starts_at = starts_at;
      },
      get() {
        if (this.value && this.value.starts_at) {
          return this.value.starts_at;
        } else {
          return null;
        }
      }
    },

    frequencyOptions() {
      //'once','daily','weekly','monthly'
      return [
        { value: 'never', label: this.$t('global.datepicker.set_frequency.never') },
        { value: 'once', label: this.$t('global.datepicker.set_frequency.one_time') },
        { value: 'daily', label: this.$t('global.datepicker.set_frequency.daily') },
        { value: 'weekly', label: this.$t('global.datepicker.set_frequency.weekly') },
        { value: 'monthly', label: this.$t('global.datepicker.set_frequency.monthly') }
      ];
    },
  },
  // watch: {
  //   'value.enabled': {
  //     handler: function(val, ) {
  //       console.log('value.enabled', val)
  //     },
  //     deep: true
  //   }
  // },
  methods: {
    reminderSetValidator: function reminderSetValidator(value) {
      //console.log('validator reminderSet', value);
      if (value.enabled === false) {
        return true;
      }
      if (value && value.frequency) {
        if (value.frequency === 'never') {
          return true;
        }
        if (value.frequency !== 'never' && value.starts_at !== null) {
          return true;
        }
        return false;
      } else {
        return false;
      }

    },
    getMinDate(){
      var actionPlanMin = new Date(this.$root.$children[0].$children[1].$children[5].$children[0].$children[0].value);
      console.log(actionPlanMin);
      // var today = addToDate((new Date()), { days: 1 });
      // return today > actionPlanMin ? actionPlanMin  : today;
      return today > actionPlanMin ? today : actionPlanMin;
      // return new Date();
    }
  }
};

</script>
<style lang="css"
    scoped>
.reminder-interval-picker {}

.reminder-interval-picker {}

</style>
