<template>
  <h2
    class="block w-full my-2 py-2 px-1 text-center text-white capitalize text-2xl"
    style="background-color: #1d9cc4"
  >
    {{ dayName }}
  </h2>
</template>

<script>
export default {
  props: ["day"],
  data() {
    return {
      dayName: ""
    };
  },
  beforeMount() {
    switch (this.day) {
      case "monday":
        this.dayName = "lundi";
        break;
      case "tuesday":
        this.dayName = "mardi";
        break;
      case "wednesday":
        this.dayName = "mercredi";
        break;
      case "thursday":
        this.dayName = "jeudi";
        break;
      case "friday":
        this.dayName = "vendredi";
        break;
      case "saturday":
        this.dayName = "samedi";
        break;
      case "sunday":
        this.dayName = "dimanche";
        break;
    }
  }
};
</script>


