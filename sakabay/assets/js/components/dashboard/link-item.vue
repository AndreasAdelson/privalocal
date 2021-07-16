<template>
  <div>
    <a
      v-if="!disabled"
      :href="url"
    >
      <div
        class="card"
      >
        <div class="card-body">
          <div class="stat-widget-five">
            <div
              :class="classColor"
              class="stat-icon dib"
            >
              <font-awesome-icon
                class="mr-2"
                :icon="['fas', iconLabel]"
              />
            </div>
            <div class="stat-content">
              <div
                v-if="smallText"
                class="text-left dib"
              >
                <div class="stat-text">
                  <span class="count" />
                </div>
                <div class="stat-heading">{{ buttonText }}</div>
              </div>
              <div v-else>
                <div class="stat-text">
                  {{ buttonText }}<span class="count" />
                </div>
                <div class="stat-heading" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </a>
    <div
      v-else
      class="card isDisabled"
    >
      <div class="card-body">
        <div class="stat-widget-five">
          <div
            :class="classColor"
            class="stat-icon dib"
          >
            <font-awesome-icon
              class="mr-2"
              :icon="['fas', iconLabel]"
            />
          </div>
          <div class="stat-content">
            <div
              v-if="smallText"
              class="text-left dib"
            >
              <div class="stat-text">
                <span class="count" />
              </div>
              <div class="stat-heading">
                {{ buttonText }}
              </div>
            </div>
            <div v-else>
              <div class="stat-text">
                {{ buttonText }}<span class="count" />
              </div>
              <div class="stat-heading" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    props: {
      url: {
        type: String,
        default: ''
      },
      iconLabel: {
        type: String,
        default: null
      },
      buttonText: {
        type: String,
        default: null
      },
      classColor: {
        type: String,
        default: null
      },
      smallText: {
        type: Boolean,
        default: false
      },
      disabled: {
        type: Boolean,
        default: false
      }
    },

  };
</script>
