<template>
<div>
  <h3 class="behavior-name">{{ reportScore.behavior.name_key_translated }}</h3>
  <p class="behavior-text">{{ reportScore.behavior.report_text_key_translated }}</p>
  <div class="report-scores-chart">
    <div class="report-row" v-if="reportScore.self_norm > 0">
      <div class="row-label">
        {{ $t('action_plan.behavior_plot.self') }}
      </div>
      <div class="row-data">
        <div class="row-data-point" :style="{left: reportScore.self_norm + '%'}">
          {{ reportScore.self_norm }}%
        </div>
      </div>
      <div class="row-label">
        {{ $t('action_plan.behavior_plot.agreement') }}
      </div>
    </div>
    <div class="report-row" v-if="reportScore.boss_norm > 0">
      <div class="row-label">
        {{ $t('action_plan.behavior_plot.boss') }}
      </div>
      <div class="row-data">
        <div class="row-data-point" :style="{left: reportScore.boss_norm + '%'}">
          {{ reportScore.boss_norm }}%
        </div>
      </div>
      <div class="row-label">
        {{ reportScore.boss_agreement }}
      </div>
    </div>
    <div class="report-row" v-if="reportScore.peer_norm > 0">
      <div class="row-label">
        {{ $t('action_plan.behavior_plot.peer') }}
      </div>
      <div class="row-data">
        <div class="row-data-point" :style="{left: reportScore.peer_norm + '%'}">
          {{ reportScore.peer_norm }}%
        </div>
      </div>
      <div class="row-label">
        {{ reportScore.peer_agreement }}
      </div>
    </div>
    <div class="report-row" v-if="reportScore.direct_report_norm > 0">
      <div class="row-label">
        {{ $t('action_plan.behavior_plot.direct_report') }}
      </div>
      <div class="row-data">
        <div class="row-data-point" :style="{left: reportScore.direct_report_norm + '%'}">
          {{ reportScore.direct_report_norm }}%
        </div>
      </div>
      <div class="row-label">
        {{ reportScore.direct_report_agreement }}
      </div>
    </div>
    <div class="report-legend">
      <div class="legend-container">
        <div class="segment">
          <div>{{ $t('action_plan.behavior_plot.low') }}</div>
          <div>
            <div>0%</div>
            <div>20%</div>
          </div>
        </div>
        <div class="segment">
          <div>{{ $t('action_plan.behavior_plot.low_mid') }}</div>
          <div>
            <div>40%</div>
          </div>
        </div>
        <div class="segment">
          <div>{{ $t('action_plan.behavior_plot.mid_range') }}</div>
          <div>
            <div>60%</div>
          </div>
        </div>
        <div class="segment">
          <div>{{ $t('action_plan.behavior_plot.hi_mid') }}</div>
          <div>
            <div>80%</div>
          </div>
        </div>
        <div class="segment">
          <div>{{ $t('action_plan.behavior_plot.high') }}</div>
          <div>
            <div>100%</div>
          </div>
        </div>
      </div>
      <div class="legend-details">
        <div class="legend-low">{{ reportScore.behavior.low_label_key_translated }}</div>
        <div class="legend-high">{{ reportScore.behavior.high_label_key_translated }}</div>
      </div>
      <div style="clear: both;"></div>
    </div>
  </div>
</div>
</template>
<style lang="scss" scoped>
  .behavior-name{
    font-size: 1.5rem;
    margin: 0 0 10px !important;
  }
  .report-row{
    display: flex;
    flex-direction: row;
    align-items: stretch;
    & > div{
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    &:first-child{
      .row-data-point{
        background: #2179D5;
      }
    }
    &:nth-child(2){
      .row-data-point{
        background: #FFD33F;
      }
      .row-label:last-child{
        color: #FFD33F;
      }
    }
    &:nth-child(3){
      .row-data-point{
        background: #77B131;
      }
      .row-label:last-child{
        color: #77B131;
      }
    }
    &:nth-child(4){
      .row-data-point{
        background: #794F97;
      }
      .row-label:last-child{
        color: #794F97;
      }
    }
  }
  .row-label{
    width: 150px;
    &:first-child{
      align-items: flex-end;
      padding-right: 10px;
    }
    &:last-child{
      border-left: 1px solid #E8F0F3;
      padding-left: 15px;
      text-transform: capitalize;
    }
  }
  .row-data{
    position: relative;
    width: 100%;
    margin: 0 15px;
    padding: 23px 0;
    &:before{
      display: block;
      content: '';
      position: absolute;
      left: 0;
      right: 0;
      top: 50%;
      border: 1px solid #D1D9DC;
      z-index: 10;
    }
  }
  .row-data-point{
    position: absolute;
    width: 40px;
    height: 40px;
    border-radius: 20px;
    transform: translate(-50%, -50%);
    text-align: center;
    line-height: 40px;
    vertical-align: middle;
    z-index: 20;
    top: 50%;
    font-weight: bold;
    color: #FFF;
    transition: all 0.2s;
  }
  .report-legend{
    padding: 20px 115px 0;
  }
  .legend-container{
    display: flex;
    flex-direction: row;
    .segment{
      width: 100%;
      white-space: nowrap;
      
      & > div:first-child{
        text-align: center;
        font-weight: bold;
        text-transform: uppercase;
        background: #BCC2C5;
        padding: 8px;
      }
      & > div:last-child{
        font-size: 1.5rem;
        padding: 6px 0;
        div:first-child{
          float: left;
        }
        div:last-child{
          float: right;
          text-align: right;
        }
        div:first-child:last-child{
          float: right;
          text-align: right;
        }
      }
      &:nth-child(1){
      }
      &:nth-child(2){
        & > div:first-child{
          background: #D1D9DC;
        }
      }
      &:nth-child(3){
        & > div:first-child{
          background: #E8F0F3;
        }
      }
      &:nth-child(4){
        & > div:first-child{
          background: #D1D9DC;
        }
      }
    }
  }
  .legend-details{
    padding-top: 5px;
    & > div{
      display: inline-block;
      width: 30%;
      &.legend-high{
        float: right;
        text-align: right;
      }
    }
  }
</style>
<script>
export default {
  props: ['reportScore'],
  components: {
  },
  methods: {
  },
  mounted() {
  }
};
</script>
