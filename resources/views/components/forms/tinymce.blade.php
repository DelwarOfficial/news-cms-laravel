@props([
    'name',
    'id' => null,
    'value' => '',
    'height' => 420,
    'placeholder' => 'Write the story body...',
])

@php
    $editorId = $id ?: str_replace(['[', ']'], ['_', ''], $name);
@endphp

<textarea
    id="{{ $editorId }}"
    name="{{ $name }}"
    class="js-tinymce-editor w-full min-h-80 rounded-xl border border-gray-200 px-4 py-3 text-sm leading-7 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
    data-tinymce-height="{{ $height }}"
    data-tinymce-upload-url="{{ route('admin.tinymce.upload') }}"
    data-tinymce-placeholder="{{ $placeholder }}"
>{{ $value }}</textarea>

@once
    @push('scripts')
        <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (!window.tinymce) return;

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const isDarkMode = () => document.documentElement.classList.contains('dark');
                const uploadUrl = document.querySelector('textarea.js-tinymce-editor')?.dataset.tinymceUploadUrl;

                const initTinyMce = () => tinymce.init({
                    selector: 'textarea.js-tinymce-editor',
                    base_url: '{{ asset('js/tinymce') }}',
                    suffix: '.min',
                    license_key: 'gpl',
                    height: {{ (int) $height }},
                    menubar: false,
                    branding: false,
                    promotion: false,
                    convert_urls: false,
                    relative_urls: false,
                    remove_script_host: false,
                    automatic_uploads: true,
                    images_upload_credentials: true,
                    images_upload_handler: (blobInfo) => {
                        const formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());

                        return fetch(uploadUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            body: formData,
                        })
                            .then((response) => {
                                if (!response.ok) throw new Error('Image upload failed.');
                                return response.json();
                            })
                            .then((json) => {
                                if (!json.location) throw new Error('Upload response did not include a location.');
                                return json.location;
                            });
                    },
                    file_picker_types: 'image',
                    image_title: true,
                    image_caption: true,
                    paste_data_images: false,
                    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
                    toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist blockquote | link image media table | ltr rtl | removeformat code preview fullscreen',
                    toolbar_mode: 'sliding',
                    block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Quote=blockquote',
                    skin: isDarkMode() ? 'oxide-dark' : 'oxide',
                    content_css: isDarkMode() ? 'dark' : 'default',
                    content_style: 'body{font-family:Inter,"Noto Sans Bengali",system-ui,sans-serif;font-size:16px;line-height:1.8} img{max-width:100%;height:auto} blockquote{border-left:4px solid #cbd5e1;margin:1rem 0;padding:.5rem 1rem;color:#64748b}',
                    setup: (editor) => {
                        editor.on('init', () => {
                            const placeholder = editor.getElement()?.dataset.tinymcePlaceholder;
                            if (placeholder) editor.getContainer()?.setAttribute('aria-label', placeholder);
                        });
                        editor.on('change input undo redo keyup paste', () => {
                            editor.save();
                            editor.getElement()?.dispatchEvent(new Event('input', { bubbles: true }));
                        });
                    },
                });

                initTinyMce();

                window.addEventListener('dark-mode-toggle', () => {
                    const editors = tinymce.editors.filter((editor) => editor.getElement()?.classList.contains('js-tinymce-editor'));
                    const snapshots = editors.map((editor) => [editor.id, editor.getContent()]);
                    editors.forEach((editor) => editor.remove());
                    window.setTimeout(() => {
                        initTinyMce();
                        window.setTimeout(() => {
                            snapshots.forEach(([id, content]) => tinymce.get(id)?.setContent(content));
                        }, 50);
                    }, 50);
                });

                document.querySelectorAll('form').forEach((form) => {
                    form.addEventListener('submit', () => window.tinymce?.triggerSave());
                });
            });
        </script>
    @endpush
@endonce
