<script>
import FormBelongsToField from '@/fields/Form/BelongsToField';
import isNil from 'lodash/isNil'

export default {
    data: () => ({
        dependsOnValues: {},
        watcherDebounce: null,
        watcherDebounceTimeout: 200,
    }),
    extends: FormBelongsToField,
    created() {
        if (this.field.dependsOn && Object.keys(this.field.dependsOn).length) {
            this.registerDependencyWatchers(
                this.findRequiredComponents(this.$root)
            );
        }
    },
    beforeUnmount() {
        if (this.watcherDebounce) {
            clearTimeout(this.watcherDebounce);
        }
    },
    methods: {
        findRequiredComponents(root) {
            var components = {};
            // Find required components
            this.walk(root.$.vnode, (component) => {
                var key = this.componentDependencyKey(component);
                if (key && ! components[key]) { 
                    components[key] = component;
                }
            })
            return components;
        },

        registerDependencyWatchers(components) {
            if (Object.keys(components).length != Object.keys(this.field.dependsOn).length) {
                return
            }

            // register watchers
            for (const [key, component] of Object.entries(components)) {
                // set value by default
                this.dependsOnValues[key] = null

                // BelongsTo field
                if (component.selectedResourceId !== undefined) {
                    if (component.isSearchable) {
                        // register wathcer
                        component.$watch('selectedResource', ({value}) => this.dependencyWatcher(key, value), {immediate: true});

                        // call initially
                        this.dependencyWatcher(key, component.selectedResource?.value);
                    } else {
                        // register wathcer
                        component.$watch('selectedResourceId', (value) => this.dependencyWatcher(key, value), {immediate: true});

                        // call initially
                        this.dependencyWatcher(key, component.selectedResourceId);
                    }
                }
                // Text field
                else if (component.value !== undefined) {
                    // register wathcer
                    component.$watch('value', (value) => this.dependencyWatcher(key, value), {immediate: true});

                    // call initially
                    this.dependencyWatcher(key, component.value);
                }
            }
        },
        componentDependencyKey(component) {
            if (component.field === undefined) {
                return false;
            }

            return this.field.dependsOn[component.field.attribute] ? component.field.attribute : null;
        },
        dependencyWatcher(key, value) {
            clearTimeout(this.watcherDebounce);
            this.watcherDebounce = setTimeout(() => {
                if (value === this.dependsOnValues[key]) {
                    return;
                }
                this.dependsOnValues[key] = value;

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
                    dependsOnValues: this.dependsOnValues,
                },
            }
        },
    },
}
</script>
