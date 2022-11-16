<script setup>
import TableRow from "./TableRow.vue";

defineProps({
    fonts: Array
})

const emit = defineEmits(['getFonts'])

function getFonts() {
    emit('getFonts')
}
</script>

<template>
    <div class="flex flex-col items-center justify-center w-full">
        <div class="w-[95%] max-w-[800px] p-4 border">
            <div class="flex flex-col items-start gap-1 w-full mb-5">
                <h2 class="text-2xl font-semibold">Our Fonts</h2>

                <p class="text-gray-400 text-[14px]">Browse a list Zepto fonts to build your font group.</p>
            </div>

            <table class="border-collapse w-full border border-slate-400 bg-white text-sm shadow-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="w-5/12 border border-slate-300 font-semibold px-4 py-2 text-slate-900 text-left">FONT NAME</th>
                        <th class="w-5/12 border border-slate-300 font-semibold px-4 py-2 text-slate-900 text-left">PREVIEW</th>
                        <th class="w-2/12 border border-slate-300 font-semibold px-4 py-2 text-slate-900 text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    <TableRow
                        v-for="font in fonts" :key="font.id"
                        :font="font"

                        @getFonts="getFonts"
                    />
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>

</style>
