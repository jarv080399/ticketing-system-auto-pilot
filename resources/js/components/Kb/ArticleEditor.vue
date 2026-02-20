<template>
    <div class="glass-card rounded-xl overflow-hidden flex flex-col h-[700px]">
        <!-- Editor Header (Tools) -->
        <div v-if="editor" class="bg-surface-light border-b border-glass-border p-2 flex flex-wrap gap-1 items-center sticky top-0 z-10">
            <button @click="editor.chain().focus().toggleBold().run()" :class="{ 'bg-primary/20 text-primary': editor.isActive('bold') }" class="p-2 rounded hover:bg-white/5 text-text-main transition-colors" title="Bold">
                <strong>B</strong>
            </button>
            <button @click="editor.chain().focus().toggleItalic().run()" :class="{ 'bg-primary/20 text-primary': editor.isActive('italic') }" class="p-2 rounded hover:bg-white/5 text-text-main transition-colors italic" title="Italic">
                I
            </button>
            <button @click="editor.chain().focus().toggleStrike().run()" :class="{ 'bg-primary/20 text-primary': editor.isActive('strike') }" class="p-2 rounded hover:bg-white/5 text-text-main transition-colors line-through" title="Strikethrough">
                S
            </button>
            <div class="w-px h-6 bg-glass-border mx-1"></div>
            <button @click="editor.chain().focus().setParagraph().run()" :class="{ 'bg-primary/20 text-primary': editor.isActive('paragraph') }" class="p-2 rounded hover:bg-white/5 text-text-main transition-colors text-sm" title="Paragraph">
                P
            </button>
            <button @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'bg-primary/20 text-primary': editor.isActive('heading', { level: 2 }) }" class="p-2 rounded hover:bg-white/5 text-text-main transition-colors font-bold text-sm" title="Heading 2">
                H2
            </button>
            <button @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="{ 'bg-primary/20 text-primary': editor.isActive('heading', { level: 3 }) }" class="p-2 rounded hover:bg-white/5 text-text-main transition-colors font-bold text-sm" title="Heading 3">
                H3
            </button>
            <div class="w-px h-6 bg-glass-border mx-1"></div>
            <button @click="editor.chain().focus().toggleBulletList().run()" :class="{ 'bg-primary/20 text-primary': editor.isActive('bulletList') }" class="p-2 rounded hover:bg-white/5 text-text-main transition-colors text-sm" title="Bullet List">
                • List
            </button>
            <button @click="editor.chain().focus().toggleOrderedList().run()" :class="{ 'bg-primary/20 text-primary': editor.isActive('orderedList') }" class="p-2 rounded hover:bg-white/5 text-text-main transition-colors text-sm" title="Numbered List">
                1. List
            </button>
            <button @click="editor.chain().focus().toggleBlockquote().run()" :class="{ 'bg-primary/20 text-primary': editor.isActive('blockquote') }" class="p-2 rounded hover:bg-white/5 text-text-main transition-colors text-sm" title="Quote">
                " Quote
            </button>
            <div class="w-px h-6 bg-glass-border mx-1"></div>
            <button @click="setLink" :class="{ 'bg-primary/20 text-primary': editor.isActive('link') }" class="p-2 rounded hover:bg-white/5 text-text-main transition-colors text-sm" title="Link">
                🔗 Link
            </button>
            <button @click="editor.chain().focus().undo().run()" :disabled="!editor.can().undo()" class="p-2 rounded hover:bg-white/5 text-text-dim disabled:opacity-30 transition-colors text-sm ml-auto">
                ↩ Undo
            </button>
            <button @click="editor.chain().focus().redo().run()" :disabled="!editor.can().redo()" class="p-2 rounded hover:bg-white/5 text-text-dim disabled:opacity-30 transition-colors text-sm">
                ↪ Redo
            </button>
        </div>

        <!-- TipTap Content -->
        <div class="flex-1 overflow-y-auto bg-surface/50 p-6">
            <editor-content :editor="editor" class="prose prose-invert max-w-none focus:outline-none min-h-[300px]" />
        </div>
    </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Editor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);
const editor = ref(null);

onMounted(() => {
    editor.value = new Editor({
        extensions: [
            StarterKit,
            Link.configure({
                openOnClick: false,
                HTMLAttributes: {
                    class: 'text-primary hover:text-primary-light underline cursor-pointer',
                },
            }),
        ],
        content: props.modelValue,
        onUpdate: () => {
            emit('update:modelValue', editor.value.getHTML());
        },
        editorProps: {
            attributes: {
                class: 'prose-sm sm:prose lg:prose-lg prose-invert focus:outline-none max-w-none',
            },
        },
    });
});

watch(() => props.modelValue, (value) => {
    const isSame = editor.value.getHTML() === value;
    if (isSame) return;
    editor.value.commands.setContent(value, false);
});

onBeforeUnmount(() => {
    if (editor.value) editor.value.destroy();
});

const setLink = () => {
    const previousUrl = editor.value.getAttributes('link').href;
    const url = window.prompt('URL', previousUrl);

    if (url === null) return;
    if (url === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
        return;
    }
    editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
};
</script>

<style>
/* Scoped prose styles are applied via Tailwind plugin, but we tweak empty node slightly */
.ProseMirror p.is-editor-empty:first-child::before {
  content: attr(data-placeholder);
  float: left;
  color: #a0aec0;
  pointer-events: none;
  height: 0;
}
</style>
