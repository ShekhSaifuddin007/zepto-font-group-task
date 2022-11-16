<script setup>
import { onMounted, ref } from "vue";
import DropZone from "./components/DropZone.vue";
import FontTable from "./components/FontTable.vue";
import CreateFontGroup from "./components/CreateFontGroup.vue";
import GroupList from "./components/GroupList.vue";

const fonts = ref([])
const fontGroups = ref([])


onMounted(() => {
    getAllFonts()
})

function getAllFonts() {
    $http.get('/ttf-files')
        .then((response) => {
            fonts.value = response.data
        })
}
</script>

<template>
    <div class="container mx-auto mb-20">
        <div class="dropzone-area flex flex-col items-center justify-center mt-5">
            <h2 class="text-2xl mb-5">Upload your file</h2>

            <DropZone @getFonts="getAllFonts" />
        </div>

        <FontTable :fonts="fonts" @getFonts="getAllFonts" />

        <CreateFontGroup />

        <GroupList />
    </div>

    <notifications />
</template>
