<template>
    <div>
        <div class="flex justify-between items-center">
            <div class="w-full flex flex-col sm:items-center sm:justify-between">
                <div v-if="$slots.title"
                     class="flex justify-start w-full font-semibold">
                    <slot name="title"/>
                </div>

                <div v-if="$slots.default" class="w-full flex justify-start mt-xs">
                    <slot/>
                </div>
            </div>
        </div>
        <div v-if="$slots.footer" class="mt-xs">
            <slot name="footer"/>
        </div>
    </div>
</template>
<script setup>
</script>
