<template>
    <tr>
        <td width="1%">
            <button type="button" class="btn btn-xs btn-danger" @click="$emit('remove')">
                <i class="fa fa-trash"></i>
            </button>
        </td>

        <td>
            <ul class="list-unstyled m-a-0">
                <li v-for="language in languages">
                    <b>{{ language.iso_code | uppercase }}:</b>
                    <x-editable v-model="attribute.translations[language.iso_code].name"></x-editable>
                </li>
            </ul>
        </td>

        <td v-show="hasIcon" class="icon-td">
            <img :src="attribute.image_url || 'https://placehold.it/150'" alt="Preview" class="img-thumbnail">
            <label class="custom-file px-file">
                <input type="file" class="custom-file-input" @change="handleFileChange($event)">
                <span class="custom-file-control form-control">Choose file...</span>
                <div class="px-file-buttons">
                    <button type="button" class="btn px-file-clear">Clear</button>
                    <button type="button" class="btn btn-primary px-file-browse">Browse</button>
                </div>
            </label>
        </td>
    </tr>
</template>

<script>
	import { mapState } from 'vuex';
	import XEditable from '../../XEditable';

	export default {
		props: {
			attribute: {
				type: Object,
				required: true
			},

			hasIcon: {
				type: Boolean,
				default: false
			}
		},

        computed: mapState(['languages']),

		mounted () {
			let fileInput = $(this.$el).find('.px-file');

			setTimeout(() => {
				fileInput.pxFile();
			}, 500);
		},

		methods: {
			handleFileChange (event) {
				let input = event.target;

				if (input.files && input.files[0]) {
					var reader = new FileReader();

					reader.onload = e => {
						$(this.$el).find('.icon-td img').attr('src', e.target.result);
					};

					reader.readAsDataURL(input.files[0]);

					this.attribute.image = input.files[0];
				}
			}
		},

		components: {
			XEditable
		}
	};
</script>

<style lang="scss" scoped>
    .icon-td {
        img {
            width: 100px;
            vertical-align: top;
            margin-right: 15px;
        }

        .px-file {
            display: inline-block !important;
            width: 250px !important;
        }
    }

    .editable-unsaved {
        font-weight: normal !important;
    }
</style>