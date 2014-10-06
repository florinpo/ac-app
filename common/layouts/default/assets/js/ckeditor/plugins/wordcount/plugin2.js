/**
 * @license Copyright (c) CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.plugins.add('wordcount', {
    lang: ['ca', 'de', 'en', 'es', 'fr', 'it', 'jp', 'no', 'pl', 'pt-BR'],
    version : 1.08,
    init: function (editor) {
        if (editor.elementMode === CKEDITOR.ELEMENT_MODE_INLINE) {
            return;
        }
        
        var defaultFormat = '<span class="cke_path_item">',
        intervalId,
        lastWordCount,
        lastCharCount = 0,
        limitReachedNotified = false,
        limitRestoredNotified = false,
        minLimitReachedNotified = false,
        minLimitRestoredNotified = false;

        // Default Config
        var defaultConfig = {
            showWordCount: true,
            showCharCount: false,
            charLimit: 'unlimited',
            minCharLimit: null,
            wordLimit: 'unlimited',
            countHTML: false
        };

        // Get Config & Lang
        var config = CKEDITOR.tools.extend(defaultConfig, editor.config.wordcount || {}, true);

        if (config.showCharCount) {
            var charLabel = editor.lang.wordcount[config.countHTML ? 'CharCountWithHTML' : 'CharCount'];

            if (config.minCharLimit != null) {
                defaultFormat += '<span class="cke_count_toggle">%charCount%</span>';
            } else {
                defaultFormat += '%charCount%';
            }
            
            if (config.charLimit != 'unlimited') {
                
                defaultFormat += '&nbsp;' + config.separator + '&nbsp;' + config.charLimit;
            }
            defaultFormat += '&nbsp;' + charLabel;
        }

        if (config.showCharCount && config.showWordCount) {
            defaultFormat += ',&nbsp;';
        }

        if (config.showWordCount) {
            defaultFormat += '%wordCount%';

            if (config.wordLimit != 'unlimited') {
                defaultFormat += '&nbsp;' + config.separator + '&nbsp;' + config.wordLimit;
            }
            defaultFormat += '&nbsp;' + editor.lang.wordcount.WordCount;
        }
        
        defaultFormat += '</span>';

        var format = defaultFormat;

        CKEDITOR.document.appendStyleSheet(this.path + 'css/wordcount.css');
        
        function counterId(editorInstance) {
            return 'cke_wordcount_' + editorInstance.name;
        }

        function counterElement(editorInstance) {
            return document.getElementById(counterId(editorInstance));
        }

        function strip(html) {
            var tmp = document.createElement("div");
            tmp.innerHTML = html;

            if (tmp.textContent == '' && typeof tmp.innerText == 'undefined') {
                return '0';
            }
            return tmp.textContent || tmp.innerText;
        }

        function updateCounter(editorInstance) {
            var wordCount = 0,
            charCount = 0,
            normalizedText,
            text;

            if (text = editorInstance.getData()) {
                if (config.showCharCount) {
                    if (config.countHTML) {
                        charCount = text.length;
                    } else {
                        normalizedText = text.
                        replace(/(\r\n|\n|\r)/gm, "").
                        replace(/^\s+|\s+$/g, "").
                        replace("&nbsp;", "").
                        replace(" ", "");
                        normalizedText = strip(normalizedText);

                        charCount = normalizedText.length;
                    }
                }

                if (config.showWordCount) {
                    normalizedText = text.
                    replace(/(\r\n|\n|\r)/gm, " ").
                    replace(/^\s+|\s+$/g, "").
                    replace("&nbsp;", " ");

                    normalizedText = strip(normalizedText);

                    wordCount = normalizedText.split(/\s+/).length;
                }
            }

            var html = format.replace('%wordCount%', wordCount).replace('%charCount%', charCount);

            counterElement(editorInstance).innerHTML = html;

            if (charCount == lastCharCount) {
                return true;
            }
            
            lastWordCount = wordCount;
            lastCharCount = charCount;

            // Check for word limit
            if (config.showWordCount && wordCount > config.wordLimit) {
                limitReached(editor, limitReachedNotified);
            } else if (config.showWordCount && wordCount == config.wordLimit) {
                // create snapshot to make sure only the content after the limit gets deleted
                editorInstance.fire('saveSnapshot');
            } else if (!limitRestoredNotified && wordCount < config.wordLimit) {
                limitRestored(editor);
            }

            // Check for char limit
            if (config.showCharCount && charCount > config.charLimit) {
                limitReached(editor, limitReachedNotified);
            } else if (config.showCharCount && charCount == config.charLimit) {
                // create snapshot to make sure only the content after the limit gets deleted
                editorInstance.fire('saveSnapshot');
            } else if (!limitRestoredNotified && charCount < config.charLimit) {
                limitRestored(editor);
            }
            
            // Check for char limit
            if (config.showCharCount && config.minCharLimit != null && charCount > config.minCharLimit) {
                minLimitReached(editor, minLimitReachedNotified);
            } 
            else if (config.showCharCount && config.minCharLimit != null && charCount <= config.minCharLimit) {
                minLimitRestored(editor);
            }

            return true;
        }
        
        

        function limitReached(editorInstance, notify) {
            limitReachedNotified = true;
            limitRestoredNotified = false;

            editorInstance.execCommand('undo');

            if (!notify) {
                counterElement(editorInstance).className = "cke_wordcount cke_wordcountLimitReached";
                
                editorInstance.fire('limitReached', {}, editor);
            }
            
            // lock editor
            editorInstance.config.Locked = 1;
        }

        function limitRestored(editorInstance) {
            
            limitRestoredNotified = true;
            limitReachedNotified = false;
            editorInstance.config.Locked = 0;
			
            counterElement(editorInstance).className = "cke_wordcount";
        }
        
        function minLimitReached(editorInstance, notify){
            
            minLimitReachedNotified = true;
            minLimitRestoredNotified = false;
            
            if (!notify) {
                counterElement(editorInstance).className = "cke_wordcount cke_green";
                editorInstance.fire('minLimitReached', {}, editor);
            }
            
        }
        
        function minLimitRestored(editorInstance) {
            minLimitReachedNotified = false;
            minLimitRestoredNotified = true;
            counterElement(editorInstance).className = "cke_wordcount cke_red";
        }
        
        editor.on('change', function (event) {

            updateCounter(event.editor);
        }, editor, null, 100);

        editor.on('uiSpace', function (event) {
            if(config.minCharLimit != null){
                var elClass = "cke_wordcount cke_red";
            } else {
                var elClass = "cke_wordcount";
            }
            
            if (event.data.space == 'bottom') {
                event.data.html += '<div id="' + counterId(event.editor) + '" class="' + elClass + '" style="">&nbsp;</div>';
            }
        }, editor, null, 100);
        editor.on('dataReady', function (event) {
            updateCounter(event.editor);
        }, editor, null, 100);
        /*editor.on('change', function (event) {
			
            updateCounter(event.editor);
        }, editor, null, 100);*/

        editor.on('afterPaste', function (event) {
            updateCounter(event.editor);
        }, editor, null, 100);
        /*editor.on('focus', function (event) {
            editorHasFocus = true;
            intervalId = window.setInterval(function () {
                updateCounter(editor);
            }, 300, event.editor);
        }, editor, null, 300);*/
        editor.on('blur', function () {
            if (intervalId) {
                window.clearInterval(intervalId);
            }
        }, editor, null, 300);
        
        if (!String.prototype.trim) {
            String.prototype.trim = function () {
                return this.replace(/^\s+|\s+$/g, '');
            };
        }
    }
});
