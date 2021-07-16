<template>
  <div class="row mt-3 ">
    <div class="col-12 card-skb">
      <div class="title-card-skb">
        <font-awesome-icon
          class="yellow-login-skb"
          :icon="['fas', 'file-alt']"
          size="lg"
        />
        <span class="fontPoppins underline ml-1">{{ $t('company.presentation.description') }}</span>
      </div>
      <div class="details-card">
        <div
          v-if="isSubscriptionActive && company.description_full != null"
          class-name="fontAlice fontSize18"
          v-html="company.description_full"
        />
        <nl2br
          v-else
          tag="p"
          :text="company.description_clean"
        />
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    props: {
      company: {
        type: Object,
        default: null
      },
      isSubscriptionActive: {
        type: Boolean,
        default: false
      }
    }
  };
</script>
