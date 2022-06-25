import IndexField from '@/fields/Index/BelongsToField.vue'
import DetailField from '@/fields/Detail/BelongsToField.vue'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-belongs-to-dependency', IndexField)
  app.component('detail-belongs-to-dependency', DetailField)
  app.component('form-belongs-to-dependency', FormField)
})


