<script setup>
import AttachedBlocks from "./PageAttachedBlocks.vue";
import AddExistingBlock from "./AddExistingBlock.vue";
import CreateBlock from "./CreateBlock.vue";

const props = defineProps({
    page: {
        required: true,
        type: Object
    }
})

const addBlock = (block) => {
    props.page.blocks.push(block);
}
</script>
<template>
    <div>
        <AttachedBlocks v-model="page.blocks"/>

        <div class="flex items-center mt-lg">
            <AddExistingBlock v-model="page.blocks"
                              :blocks="$page.props.blocks"
            />

            <CreateBlock @add="addBlock"/>
        </div>
    </div>
</template>
