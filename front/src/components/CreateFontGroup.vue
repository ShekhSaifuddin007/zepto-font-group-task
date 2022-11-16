<script setup>
import {onMounted, reactive, ref} from "vue";

import { useNotification } from "@kyvg/vue3-notification";

const notification = useNotification()

const fontList = ref([])

onMounted(() => {
    $http.get('/ttf-files')
        .then(res => {
            fontList.value = res.data
        })
})

const form = reactive({
    group_title: '',
    fields: [{
        id: '',
        font_name: '',
        ttf_files_id: 'null',
        size: '',
        price: ''
    }]
})

function addField() {
    form.fields.push({
        id: '',
        font_name: '',
        ttf_files_id: 'null',
        size: '',
        price: ''
    })
}

function saveFontGroup() {
    let formData = new FormData();
    formData.append('group_title', form.group_title)

    for (let i = 0; i < form.fields.length; i++) {
        formData.append('id[]', form.fields[i].id);
        formData.append('font_name[]', form.fields[i].font_name);
        formData.append('ttf_files_id[]', form.fields[i].ttf_files_id);
        formData.append('size[]', form.fields[i].size);
        formData.append('price[]', form.fields[i].price);
    }

    if (form.fields.length < 2) {
        notification.notify({
            speed: 2000,
            type: "error",
            title: "Something went wrong.!",
            text: "You have to select at least two fonts."
        });
    } else {
        $http.post('/font-groups', formData)
            .then((res) => {
                notification.notify({
                    speed: 2000,
                    type: "success",
                    title: "Success",
                    text: res.data.message
                });

                form.group_title = ''
                form.fields = [{
                    id: '',
                    font_name: '',
                    ttf_files_id: 'null',
                    size: '',
                    price: ''
                }]
            })
    }
}

function removeRow(idx) {
    form.fields.splice(idx, 1);
}
</script>

<template>
    <div class="flex flex-col items-center justify-center w-full mt-10">
        <div class="w-[95%] max-w-[800px] p-4 border">
            <div class="flex flex-col items-start gap-1 w-full mb-5">
                <h2 class="text-2xl font-semibold">Create Font Group</h2>

                <p class="text-gray-400 text-[14px]">You have to select at least two font.</p>
            </div>

            <form @submit.prevent="saveFontGroup">
                <input v-model="form.group_title" type="text" placeholder="Group Title" class="w-full border border-slate-300 focus:outline-none py-1 px-5 rounded text-[14px]">

                <div
                    v-for="(field, idx) in form.fields"
                    :key="idx"
                    class="flex items-center gap-3 w-full mt-1 p-2 bg-white shadow-lg rounded"
                >
                    <input v-model="field.font_name" type="text" placeholder="Font Name" class="w-[23%] border border-slate-300 focus:outline-none py-1 px-5 rounded text-[14px]">

                    <select v-model="field.ttf_files_id" class="w-[23%] border border-slate-300 bg-white focus:outline-none py-1 px-5 rounded text-[14px]">
                        <option value="null">Select font</option>
                        <option
                            v-for="font in fontList"
                            :key="font.id"
                            :value="font.id"
                        >{{ font.family }}</option>
                    </select>

                    <input v-model="field.size" type="number" placeholder="Specific Size" class="w-[23%] border border-slate-300 focus:outline-none py-1 px-5 rounded text-[14px]">

                    <input v-model="field.price" type="number" placeholder="Price Change" class="w-[23%] border border-slate-300 focus:outline-none py-1 px-5 rounded text-[14px]">

                    <button
                        @click.prevent="removeRow(idx)"
                        class="p-1 focus:outline-none text-red-700"
                    >&times;</button>
                </div>

                <div class="flex items-center justify-between mt-5">
                    <button
                        @click.prevent="addField"
                        class="py-0.5 px-3 focus:outline-none border border-green-900"
                    >
                        + Add Row
                    </button>
                    <button
                        type="submit"
                        class="py-0.5 px-3 focus:outline-none bg-green-900 border border-green-900 text-white"
                    >
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>

</style>
