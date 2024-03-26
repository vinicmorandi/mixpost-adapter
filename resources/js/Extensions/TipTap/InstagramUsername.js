import { mergeAttributes, Mark } from '@tiptap/core'

const InstagramUsername = Mark.create({
    name: 'instagram_name',

    addOptions() {
        return {
            HTMLAttributes: {
                class: 'font-medium'
            },
        }
    },

    parseHTML() {
        return [
            { tag: 'instagram_username' },
        ]
    },

    renderHTML({ HTMLAttributes }) {
        return ['instagram_username', mergeAttributes(this.options.HTMLAttributes, HTMLAttributes), 0]
    },
})

export default InstagramUsername;
