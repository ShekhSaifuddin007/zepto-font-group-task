<script setup>
import { useDropzone } from "vue3-dropzone";
import { reactive } from "vue";
import { useNotification } from "@kyvg/vue3-notification";

const notification = useNotification()

const state = reactive({
    files: [],
});

const emit = defineEmits(['getFonts'])

const options = reactive({
    multiple: false,
    onDrop,
    accept: '.ttf',
})

function onDrop(acceptFiles, rejectReasons) {
    console.log(acceptFiles);
    console.log(rejectReasons);

    state.files = acceptFiles

    saveFiles(acceptFiles);
}

const { getRootProps, getInputProps, isDragActive, ...rest } = useDropzone(options);

function handleClickDeleteFile(index) {
    state.files.splice(index, 1);
}


const saveFiles = (files) => {
    const formData = new FormData();
    formData.append("ttf_file", files[0]);

    $http.post('/ttf-files', formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    })
    .then((response) => {
        setTimeout(() => state.files = [], 1500)

        emit('getFonts')

        notification.notify({
            speed: 2000,
            type: "success",
            title: "Success",
            text: response.data.message
        });
    })
    .catch(({ response }) => {
        state.files = []

        notification.notify({
            speed: 2000,
            type: "error",
            title: response.data.message,
            text: response.data.errors[0]
        });
    });
};
</script>

<template>
    <div v-if="state.files.length > 0" class="files">
        <div class="file-item" v-for="(file, index) in state.files" :key="index">
            <span>{{ file.name }}</span>
            <span class="delete-file" @click="handleClickDeleteFile(index)">Delete</span>
        </div>
    </div>

    <div v-else class="dropzone cursor-pointer" v-bind="getRootProps()">
        <div class="border" :class="{ isDragActive }">
            <input v-bind="getInputProps()" />

            <div class="flex flex-col items-center justify-center gap-5">
                <svg width="24" height="24" viewBox="0 0 24 24" stroke-width="2" class="text-gray-400" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1"></path>
                    <polyline points="9 15 12 12 15 15"></polyline>
                    <line x1="12" y1="12" x2="12" y2="21"></line>
                </svg>

                <p v-if="isDragActive">Drop the files here ...</p>
                <div v-else>
                    <span class="text-gray-700 font-semibold">Click to upload</span> <span class="text-gray-500 font-semibold">or drag and drop</span>
                </div>

                <div class="text-gray-400 font-semibold">Only TTF file allowed</div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.dropzone,
.files {
    width: 95%;
    max-width: 800px;
    margin: 0 auto 40px;
    padding: 10px;
    border-radius: 8px;
    box-shadow: rgba(60, 64, 67, 0.3) 0 1px 2px 0,
    rgba(60, 64, 67, 0.15) 0 1px 3px 1px;
    font-size: 12px;
    line-height: 1.5;
}

.border {
    border: 2px dashed #ccc;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    transition: all 0.3s ease;
    background: #fff;
}

.border .isDragActive {
    border: 2px dashed #ffb300;
    background: rgb(255 167 18 / 20%);
}

.file-item {
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgb(255 167 18 / 20%);
    padding: 7px;
    padding-left: 15px;
    margin-top: 10px;
}

.file-item:first-child {
    margin-top: 0;
}

.file-item .delete-file {
    background: red;
    color: #fff;
    padding: 5px 10px;
    border-radius: 8px;
    cursor: pointer;
}
</style>
