<script setup>
import {onMounted, reactive, ref} from "vue";

import { useNotification } from "@kyvg/vue3-notification";

const notification = useNotification()

const fontGroups = ref([])

const form = reactive({
    id: '',
    group_title: '',
    fields: []
})

const modal = ref(false)

onMounted(() => {
    getFontGroups()

    $http.get('/ttf-files')
        .then(res => {
            fontList.value = res.data
        })
})

function getFontGroups() {
    $http.get('/font-groups')
        .then(res => {
            fontGroups.value = res.data
        })
}

function editFontGroup(group) {
    $http.get('/get-font-groups', {
        params: { id: group.id }
    })
        .then(res => {
            form.id = ''
            form.group_title = ''
            form.fields = []

            form.id = res.data.id
            form.group_title = res.data.title

            res.data.font_groups.forEach(value => {
                form.fields.push({
                    id: value.id,
                    font_name: value.font_name,
                    ttf_files_id: value.ttf_files_id,
                    size: value.size,
                    price: value.price
                })
            })

            modal.value = true
        })
}

const fontList = ref([])

function addField() {
    form.fields.push({
        id: '',
        font_name: '',
        ttf_files_id: 'null',
        size: '',
        price: ''
    })
}

function closeModal() {
    form.id = ''
    form.group_title = ''
    form.fields = []

    modal.value = false
}

function updateFontGroup() {
    let formData = new FormData();
    formData.append('id', form.id)
    formData.append('group_title', form.group_title)

    for (let i = 0; i < form.fields.length; i++) {
        formData.append('font_group_id[]', form.fields[i].id);
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
        $http.post('/update-font-groups', formData)
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

                getFontGroups()

                modal.value = false
            })
    }
}

function deleteFontGroup(group) {
    if (confirm('Are you sure.?')) {
        $http.get(`/delete/font-group-title`, {
            params: { id: group.id }
        })
        .then((res) => {
            notification.notify({
                speed: 2000,
                type: "success",
                title: "Success",
                text: res.data.message
            });

            getFontGroups()
        })
    }
}

function removeRow(idx, id) {
    if (id === '') {
        form.fields.splice(idx, 1);
    } else {
        if (confirm('Are you sure.?')) {
            $http.get('/delete/font-group', {
                params: { id }
            })
            .then((res) => {
                notification.notify({
                    speed: 2000,
                    type: "success",
                    title: "Success",
                    text: res.data.message
                });

                getFontGroups()

                form.fields.splice(idx, 1);
            })
        }
    }
}
</script>

<template>
    <div class="flex flex-col items-center justify-center w-full mt-10">
        <div class="w-[95%] max-w-[800px] p-4 border">
            <div class="flex flex-col items-start gap-1 w-full mb-5">
                <h2 class="text-2xl font-semibold">Our Font Groups</h2>

                <p class="text-gray-400 text-[14px]">List of all available font groups.</p>
            </div>

            <table class="border-collapse w-full border border-slate-400 bg-white text-sm shadow-sm">
                <thead class="bg-slate-50">
                <tr>
                    <th class="w-2/12 border border-slate-300 font-semibold px-4 py-2 text-slate-900 text-left">NAME</th>
                    <th class="w-7/12 border border-slate-300 font-semibold px-4 py-2 text-slate-900 text-left">Font</th>
                    <th class="w-1/12 border border-slate-300 font-semibold px-4 py-2 text-slate-900 text-left">Count</th>
                    <th class="w-2/12 border border-slate-300 font-semibold px-4 py-2 text-slate-900 text-right"></th>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="group in fontGroups">
                        <td class="border border-slate-300 px-4 py-2 text-slate-500">{{ group.title }}</td>
                        <td class="border border-slate-300 px-4 py-2 text-slate-500">{{ group.fonts.join(', ') }}</td>
                        <td class="border border-slate-300 px-4 py-2 text-slate-500">{{ group.fonts_count }}</td>
                        <td class="border border-slate-300 px-4 py-2 text-slate-500">
                            <div class="flex items-center gap-3">
                                <a @click.prevent="editFontGroup(group)" href="#" class="text-blue-700 font-bold">
                                    Edit
                                </a>
                                <a @click.prevent="deleteFontGroup(group)" href="#" class="text-red-700 font-bold">
                                    Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Modal -->
    <div v-if="modal" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[50rem]">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex flex-col items-start gap-1 w-full mb-5">
                            <h2 class="text-2xl font-semibold flex items-center justify-between w-full">
                                <span>Update Font Group</span>
                                <button type="button" class="text-red-700 p-1 text-3xl" @click.prevent="closeModal">&times;</button>
                            </h2>

                            <p class="text-gray-400 text-[14px]">You have to select at least two font.</p>
                        </div>

                        <form @submit.prevent="updateFontGroup">
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
                                    @click.prevent="removeRow(idx, field.id)"
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
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
