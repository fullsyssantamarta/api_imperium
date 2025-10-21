<template>
  <div class="card">
    <div class="card-header">Lista de Documentos</div>

    <div class="card-body">
      <el-table :data="tableData" style="width: 100%">
        <el-table-column prop="key" label="#" width="80"></el-table-column>
        <el-table-column prop="number" label="Numero" width="100"></el-table-column>
        <el-table-column prop="client" label="Cliente" width="180"></el-table-column>
        <el-table-column prop="currency" label="Moneda" width="180"></el-table-column>
        <el-table-column prop="date" label="Fecha" width="180"></el-table-column>
        <el-table-column prop="sale" label="Venta" width="150"></el-table-column>
        <el-table-column prop="total_discount" label="Descuento" width="150"></el-table-column>
        <el-table-column prop="total_tax" label="Impuesto" width="150"></el-table-column>
        <el-table-column prop="subtotal" label="Sub Total" width="150"></el-table-column>
        <el-table-column prop="total" label="Total" width="150"></el-table-column>
        <el-table-column fixed="right" label="XML" width="120">
          <template slot-scope="scope">

            <a :href="`${resource}/downloadxml/${scope.row.xml}`" target="_blank" class="btn btn-xs btn-info waves-effect waves-light"><i class="fa fa-download"></i></a>
          
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="PDF" width="120">
          <template slot-scope="scope">
            <button
              type="button"
              class="btn btn-xs btn-info waves-effect waves-light"
              @click.prevent="handleDownloadPdf(scope.row)"
            >
              <i class="fa fa-download"></i>
            </button>
          </template>
        </el-table-column>
      </el-table>
    </div>
  </div>
</template>
<style>
.extend {
  width: 100%;
}
</style>
<script>
export default {
  components: {},
  data() {
    return {
      resource: "documents",
      tableData: []
    };
  },
  created() {
      this.getRecords();
  },
  methods: {
    handleDownloadPdf(row) {
      if (!row || !row.pdf) {
        return;
      }

      this.$msgbox({
        title: 'Formato del PDF',
        message: 'Selecciona el formato en el que deseas descargar el documento.',
        confirmButtonText: 'Carta (Letter)',
        cancelButtonText: 'A4',
        showClose: true,
        showCancelButton: true,
        distinguishCancelAndClose: true,
        closeOnClickModal: false,
        closeOnPressEscape: false,
        type: 'info'
      })
        .then(() => {
          this.openPdf(row, 'letter');
        })
        .catch(action => {
          if (action === 'cancel') {
            this.openPdf(row, 'a4');
          }
        });
    },
    openPdf(row, format) {
      const sanitizedFormat = (format || '').toLowerCase();
      const acceptedFormats = ['letter', 'carta', 'a4'];
      const finalFormat = acceptedFormats.includes(sanitizedFormat)
        ? sanitizedFormat
        : 'letter';

      const url = `/${this.resource}/downloadpdf/${row.pdf}?format=${finalFormat}`;
      window.open(url, '_blank');
    },
    clickDownload(format) {
     /* window.open(
        `/${this.resource}/download/${this.form.external_id}/${format}`,
        "_blank"
      );*/
    },
   
    getRecords() {
      return new Promise((resolve, reject) => {
        this.$http
          .get(`/${this.resource}/records`)
          .then(response => {
            this.tableData = response.data.data
          })
          .catch(error => {})
          .then(() => {});
      });
    }
  }
};
</script>
