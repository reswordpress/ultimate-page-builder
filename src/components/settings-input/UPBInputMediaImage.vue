<template>
    <li :class="typeClass()" v-show="isRequired">
        <div class="form-group">
            <label>
                <div class="title-wrapper">
                    <i v-if="attributes.deviceIcon" :class="deviceClass" :title="attributes.deviceTitle"></i>
                    <span class="title" v-text="attributes.title"></span>
                </div>
            </label>

            <div v-upb-media class="attachment-media-view">
                <div v-if="input" class="preview">
                    <img :src="src" alt="" draggable="false">
                </div>

                <div v-else class="placeholder" v-text="attributes.placeholder"></div>

                <div class="actions">
                    <button v-show="input" type="button" class="button remove-button" v-text="attributes.buttons.remove"></button>
                    <button type="button" class="button new-button" v-text="attributes.buttons.choose"></button>
                    <div class="clear"></div>
                </div>
            </div>
            <p class="description" v-if="attributes.desc" v-html="attributes.desc"></p>
        </div>
    </li>
</template>
<script src="./UPBInputMediaImage.js"></script>