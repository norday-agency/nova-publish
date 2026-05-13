<template>
  <div>
    <Head :title="__('Publish a new site')" />
    <heading class="mb-6">{{ __("Publish") }}</heading>

    <p class="mb-6">
      {{ __("Publish the site to make your changes visible to the public") }}
    </p>

    <Button @click="publish" :disabled="!!publishing" class="mb-6">
      {{ __("Publish a new site") }}
    </Button>

    <p v-if="error" class="error text-error-message mb-6">
      {{
        __("Something went wrong. Please contact Norday. This is the error:")
      }}
      "{{ error }}"
    </p>

    <p v-if="lastRun && lastRun.status === 'completed'" class="mb-6">
      {{
        __("Website published last at :date", {
          date: formatDate(lastRun.updated_at),
        })
      }}
      <span v-if="lastRun.conclusion === 'failure'">
        {{ __("Unfortunately this went wrong. Please contact Norday.") }}
      </span>
    </p>

    <p v-if="lastRun && lastRun.status !== 'completed'">
      {{
        __("Started your publication at :date, please wait a few minutes.", {
          date: formatDate(lastRun.created_at),
        })
      }}
    </p>
  </div>
</template>

<script>
import { Button } from "laravel-nova-ui";

export default {
  components: { Button },
  mounted() {
    this.updateStatus();
    this.startStatusRefresh();
  },
  props: {
    publishing: {
      type: Boolean,
      default: true,
    },
    lastRun: Object,
    error: String,
  },
  data() {
    return {
      error: "",
      publishing: false,
      lastRun: undefined,
      currentLocale: Nova.config("currentLocale"),
    };
  },
  methods: {
    publish() {
      this.publishing = true;
      Nova.request()
        .post("/nova-vendor/publish/publish")
        .then(() => {
          this.error = "";
          this.updateStatus();
        })
        .catch((error) => {
          this.error = error.response.data.message || error.message;
          this.publishing = false;
        });
    },
    updateStatus() {
      Nova.request()
        .get("/nova-vendor/publish/last-publish-run")
        .then((lastRun) => {
          this.lastRun = lastRun.data;
          this.publishing = lastRun.data.status !== "completed";
          this.error = "";
        })
        .catch((error) => {
          this.error = error?.response.data.message || error.message;
        });
    },
    startStatusRefresh() {
      window.setInterval(() => {
        this.updateStatus();
      }, 10000);
    },
    formatDate(date) {
      return new Intl.DateTimeFormat(Nova.config("currentLocale"), {
        dateStyle: "full",
        timeStyle: "long",
      }).format(new Date(date));
    },
  },
};
</script>

<style>
/* Scoped Styles */
</style>
