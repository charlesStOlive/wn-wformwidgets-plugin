<?php 
return [
    'froala' => [
        'html_toolbar_buttons' => [
            'full' => 'undo,redo,bold,italic,underline,paragraphFormat,paragraphStyle,inlineStyle,strikeThrough,subscript,superscript,clearFormatting,fontFamily,fontSize,color,emoticons,-,selectAll,align,formatOL,formatUL,outdent,indent,quote,insertHR,insertLink,insertImage,insertVideo,insertAudio,insertFile,insertTable,selectAll,html,fullscreen',
            'minimal' => 'undo,redo,bold,italic,underline,strikeThrough,subscript,superscript,',
            'minimalist' => 'bold,italic',
            'default' => 'undo,redo,bold,italic,underline,subscript,superscript,paragraphFormat,clearFormatting,color,selectAll,align,formatOL,formatUL,outdent,indent,quote,insertHR,html,fullscreen',
            'insert' => 'undo,redo,bold,italic,underline,subscript,superscript,paragraphFormat,clearFormatting,color,selectAll,align,formatOL,formatUL,outdent,indent,quote,insertHR,insertLink,insertImage,insertTable,html,fullscreen',
        ],
        "html_style_image" => [],
        "html_style_link" => [
            "text-primary" => "Couleur primaire",
            "text-secondary" => "Couleur secondaire"
        ],
        "html_style_paragraph" => [
            "text-white bg-primary" => "Primaire",
            "text-white bg-secondary" => "Secondaire"
        ],
        "html_style_table" => [
            "oc-dashed-borders" => "Dashed Borders",
            "oc-alternate-rows" => "Alternate Rows"
        ],
        "html_style_table_cell" => [
            "oc-cell-highlighted" => "Highlighted",
            "oc-cell-thick-border" => "Thick Border"
        ],
        "html_paragraph_formats" => [
            "N" => "Normal",
            "H1" => "H1",
            "H2" => "H2",
            "H3" => "H3",
            "H4" => "H4",
            "PRE" => "Code"
        ],
        "html_allow_empty_tags" => "textarea, a, iframe, object, video, style, script, .fa, .fr-emoticon, .fr-inner, path, line, hr, i",
        "html_allow_tags" => "a, abbr, address, area, article, aside, audio, b, bdi, bdo, blockquote, br, button, canvas, caption, cite, code, col, colgroup, datalist, dd, del, details, dfn, dialog, div, dl, dt, em, embed, fieldset, figcaption, figure, footer, form, h1, h2, h3, h4, h5, h6, header, hgroup, hr, i, iframe, img, input, ins, kbd, keygen, label, legend, li, link, main, map, mark, menu, menuitem, meter, nav, noscript, object, ol, optgroup, option, output, p, param, pre, progress, queue, rp, rt, ruby, s, samp, script, style, section, select, small, source, span, strike, strong, sub, summary, sup, table, tbody, td, textarea, tfoot, th, thead, time, title, tr, track, u, ul, var, video, wbr",
        "html_allow_attributes" => "accept, accept-charset, accesskey, action, align, allowfullscreen, allowtransparency, alt, aria-.*, async, autocomplete, autofocus, autoplay, autosave, background, bgcolor, border, charset, cellpadding, cellspacing, checked, cite, class, color, cols, colspan, content, contenteditable, contextmenu, controls, coords, data, data-.*, datetime, default, defer, dir, dirname, disabled, download, draggable, dropzone, enctype, for, form, formaction, frameborder, headers, height, hidden, high, href, hreflang, http-equiv, icon, id, ismap, itemprop, keytype, kind, label, lang, language, list, loop, low, max, maxlength, media, method, min, mozallowfullscreen, multiple, muted, name, novalidate, open, optimum, pattern, ping, placeholder, playsinline, poster, preload, pubdate, radiogroup, readonly, rel, required, reversed, rows, rowspan, sandbox, scope, scoped, scrolling, seamless, selected, shape, size, sizes, span, src, srcdoc, srclang, srcset, start, step, summary, spellcheck, style, tabindex, target, title, type, translate, usemap, value, valign, webkitallowfullscreen, width, wrap",
        "html_no_wrap_tags" => "figure, script, style",
        "html_remove_tags" => "script, style, base",
        "html_line_breaker_tags" => "figure, table, hr, iframe, form, dl"
    ]
];