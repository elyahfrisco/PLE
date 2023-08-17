<template>
  <div class="actuality-card">
    <div class="actuality-card__title">{{ title }}</div>
    <div class="actuality-card__timer">{{ timer }}</div>
    <p class="actuality-card__content" v-html="content"></p>
    <div class="actuality-card__footer">
      <div
        class="actuality-card__footer-button __type"
        :style="`background-color: ${type.color}`"
      >
        {{ type.name ?? "Non classé" }}
      </div>
      <inertia-link
        class="actuality-card__footer-button __read"
        :href="route('post.view', id)"
        >Lire...</inertia-link
      >
    </div>
  </div>
</template>

<script>
export default {
  props: ["content", "title", "id", "type", "timer"]
};
</script>

<style lang="scss" scoped>
@import "@/Pages/Components/main.scss";

.actuality-card {
  width: 100%;
  height: 13rem;
  min-height: 13rem;
  border-radius: 0.8rem;
  border: none;
  background-color: $color-teal;
  padding: 0.5rem;
  margin: 0.5rem 0;
  text-align: start;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  justify-content: space-between;

  .actuality-card__title {
    width: 100%;
    font-weight: bold;
    font-size: 1.3rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .actuality-card__timer {
    font-style: italic;
    font-size: 12px;
  }

  .actuality-card__content {
    max-height: 5rem;
    font-size: 13px;
    overflow: hidden;
  }

  .actuality-card__footer {
    display: flex;
    justify-content: space-between;

    .actuality-card__footer-button {
      border-radius: 0.2rem;
      padding: 0.2rem 0.8rem;
      text-align: center;
      font-size: 11px;
      font-style: italic;

      &.__type {
        color: white;
      }

      &.__read {
        background-color: $color-body-bg;
        color: black;
        text-decoration: none;
      }
    }
  }
}
</style>
