<script>
import FormBelongsToField from '@/fields/Form/BelongsToField';
import isNil from 'lodash/isNil'

export default {
    data: () => ({
        dependsOnValue: null,
        watcherDebounce: null,
        watcherDebounceTimeout: 200,
    }),
    extends: FormBelongsToField,
    created() {
        if (this.field.dependsOn !== undefined) {
            this.registerDependencyWatchers(this.$root);
        }
    },
    beforeUnmount() {
        if (this.watcherDebounce) {
            clearTimeout(this.watcherDebounce);
        }
    },
    methods: {
        registerDependencyWatchers(root) {
            this.walk(root.$.vnode, component => {
                if (this.componentIsDependency(component)) {
                    if (component.selectedResourceId !== undefined) {
                        // BelongsTo field
                        if (component.isSearchable) {
                            component.$watch('selectedResource', (resource) => this.dependencyWatcher(resource?.value), {immediate: true});
                            this.dependencyWatcher(component.selectedResource?.value);
                        } else {
                            component.$watch('selectedResourceId', this.dependencyWatcher, {immediate: true});
                            this.dependencyWatcher(component.selectedResourceId);
                        }
                    } else if (component.value !== undefined) {
                        component.$watch('value', this.dependencyWatcher, {immediate: true});
                        this.dependencyWatcher(component.value);
                    }
                }

            })
            
        },
        componentIsDependency(component) {
            if (component.field === undefined) {
                return false;
            }

            return component.field.attribute === this.field.dependsOn;
        },
        dependencyWatcher(value) {
            clearTimeout(this.watcherDebounce);
            this.watcherDebounce = setTimeout(() => {
                if (value === this.dependsOnValue) {
                    return;
                }
                this.dependsOnValue = value;

                this.clearSelection();
                this.$nextTick(() => {
                    this.initializeComponent();
                });

                this.watcherDebounce = null;
            }, this.watcherDebounceTimeout);
        },

        walk(vnode, cb) {
            if (!vnode) return;

            if (vnode.component) {
                const proxy = vnode.component.proxy;
                if (proxy) cb(vnode.component.proxy);
                this.walk(vnode.component.subTree, cb);
            } else if (vnode.shapeFlag & 16) {
                const vnodes = vnode.children;
                for (let i = 0; i < vnodes.length; i++) {
                    this.walk(vnodes[i], cb);
                }
            }
        },
    },

    computed: {
        queryParams() {
            return {
                params: {
                    current: this.selectedResourceId,
                    first: this.shouldLoadFirstResource,
                    search: this.search,
                    withTrashed: this.withTrashed,
                    resourceId: this.resourceId,
                    viaResource: this.viaResource,
                    viaResourceId: this.viaResourceId,
                    viaRelationship: this.viaRelationship,
                    editing: true,
                    editMode: isNil(this.resourceId) || this.resourceId === '' ? 'create' : 'update',
                    dependsOnValue: this.dependsOnValue,
                },
            }
        },
    },
}
</script>
