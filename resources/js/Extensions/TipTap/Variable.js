import { Extension } from '@tiptap/core'
import { Plugin } from '@tiptap/pm/state'
import { Decoration, DecorationSet } from '@tiptap/pm/view'

const Variable = Extension.create({
    name: 'variable',

    addProseMirrorPlugins() {
        return [
            new Plugin({
                state: {
                    init(_, { doc }) {
                        return findVariable(doc)
                    },
                    apply(transaction, oldState) {
                        return transaction.docChanged ? findVariable(transaction.doc) : oldState
                    },
                },
                props: {
                    decorations(state) {
                        return this.getState(state)
                    },
                },
            }),
        ]
    },
})

const findVariable = (doc) => {
    const brace = /{{([a-zA-Z0-9_]+)}}/g;
    const decorations = [];

    doc.descendants((node, position) => {
        if (!node.text) {
            return
        }

        Array.from(node.text.matchAll(brace)).forEach(match => {
            const variable = match[0]
            const index = match.index || 0
            const from = position + index
            const to = from + variable.length
            const decoration = Decoration.inline(from, to, {
                class: 'text-primary-500'
            })

            decorations.push(decoration)
        })
    })

    return DecorationSet.create(doc, decorations)
}

export default Variable;
