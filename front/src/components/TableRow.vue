<script setup>
import {ref} from "vue";

const props = defineProps({
    font: Object
})

const emit = defineEmits(['getFonts'])

const family = ref(props.font.family)
const url = ref(props.font.font_url)

const fontFileUrl = `url(${url.value}) format('truetype')`

function deleteFont(font) {
    let formData = new FormData();
    formData.append('id', font.id)

    if (confirm('Are you sure.?')) {
        $http.post(`/delete/file`, formData)
            .then(() => {
                emit('getFonts')
            })
    }
}
</script>

<template>
    <tr>
        <td class="border border-slate-300 px-4 py-2 text-slate-500">{{ props.font.family }}</td>
        <td class="border border-slate-300 px-4 py-2 text-slate-500 apply-font">Example Style</td>
        <td class="border border-slate-300 px-4 py-2 text-slate-500">
            <a href="#" class="text-red-700 font-bold" @click.prevent="deleteFont(props.font)">
                Delete
            </a>
        </td>
    </tr>
</template>

<style scoped>
@font-face {
    font-family: v-bind(family);
    src: v-bind(fontFileUrl);
}

.apply-font {
    font-family: v-bind(family), serif;
}
</style>
