<template>
  <b-container
    fluid
    class="p-4 skb-body"
  >
    <b-row>
      <b-col>
        <b-img
          :src="images"
          center
          alt="Center image"
        />
        <!-- <img :src="images.logo" alt="logo"> -->
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
  import setAreaHeightMixin from 'mixins/setAreaHeightMixin';
  export default {
    components: {

    },
    mixins: [
      setAreaHeightMixin,
    ],
    data() {
      return {
        images: '/build/logo.png'
      };
    },
    methods: {
    }
  };
</script>
