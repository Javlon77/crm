@extends('layout')
@section('title', 'Sotuvlar')
@section('header-text', "Sotuvlar")
@section('content')

<div class="container tbl" >
    <!-- open show link -->
    <a href="" id="open-show-link"></a>

    <table class="table bg-white" style="width:100%;" id="c-table" data-page-length="10">
        <thead>
            <tr>
                <th>№</th>
                <th>Mijoz</th>
                <th>Tovar soni</th>
                <th>Umumiy savdo</th>
                <th>Foyda</th>
                <th>Sof foyda</th>
                <th>Sana</th>
                <th>O'chirish</th>
            </tr>          
        </thead>
        <tbody>
            
            @foreach($sales as $sale)
            <tr idx="{{ $sale->client_id }}">
                <td>{{ $loop->index+1 }}</td>
                <td>{{ $clients->keyBy('id')[$sale->client_id]->name }}</td>
                <td>{{ $sale->saleProducts()->sum('quantity') }}</td>
                <td class="seperator">{{ $sale->total_amount }}</td>
                <td class="seperator">{{ $sale->profit }}</td>
                <td class="seperator">{{ $sale->net_profit }}</td>
                <td>{{ $sale->updated_at->format('d.m.Y'); }}</td>
                <td >
                    <!-- <a href="{{ route('client-base.edit',[$sale->id])}}" class="btn btn-light edit-btn"><i class="bi bi-pencil-square"></i></a> -->
                    <form action="{{ route('sales.destroy',[$sale->id]) }}" method="post" sale="{{ $sale->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-light ms-2 delete-btn" data-bs-toggle="modal" data-bs-target="#delete-sale-modal" sale="{{ $sale->id }}"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- modal to delete sale -->
<div class="modal fade" id="delete-sale-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tasdiqlash:</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        - Siz rosdan ham ushbu savdoni o'chirib tashlashni hohlaysizmi?
        <br><br>
        - Bu tizimda ma'lum bir muammolarga sabab bo'lishi mumkin!!!
        <br><br>
        - O'chirmaslik tavsiya etiladi!!!
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
        <button type="button" class="btn btn-danger confirm-delete">O'chirish</button>
      </div>
    </div>
  </div>
</div>
<!-- end of modal to delete master -->

@section('script')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/jq-3.6.0/jszip-2.5.0/dt-1.11.3/b-2.1.1/b-html5-2.1.1/b-print-2.1.1/fh-3.2.1/r-2.2.9/datatables.min.js"></script>
    <script>
        $(document).ready(function(){
            // delete-sale
            let sale_id = 0;
            $('.delete-btn').click(function(){
                sale_id = $(this).attr('sale')
             
            });

            $('.confirm-delete').click(function(){
                $('form[sale="'+sale_id+'"]').submit()
                $('#delete-sale-modal').modal('hide');
            });
            // end of delete-sale

            //number seperator to money format
            function numberWithSpaces(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
            }
            for (let i=0; i < $('.seperator').length; i++ ) {     
                $(".seperator")[i].innerText=numberWithSpaces($(".seperator")[i].innerText)+' UZS' ;
            }
            // end of number seperator to money format
            $("body").on('input' , '.seperator', function(e){
                if(this.value!==''){
                    let a = $(this).val().replace(/\s/g, '')
                    let p = parseInt(a)
                    if(isNaN(p)){
                        $(this).val('')
                    }
                    else{
                        $(this).val(numberWithSpaces(p))
                    }
                }
            });   

            $('#c-table').DataTable({
                dom: '<"make-excel" B> <"show-filter-wrapper" lf> r <"table-wrapper"t> ip',
                language: {
                    "lengthMenu": " _MENU_ tadan",
                    "zeroRecords": "Hechnarsa topilmadi",
                    "info": "_PAGES_ - _PAGE_",
                    "infoEmpty": "No records available",
                    "infoFiltered": "(Umumiy _MAX_ qayddan filtrlandi)",
                    "search":"Qidirish:",
                    "Next":"dsa",
                    "paginate": {
                        "previous": "<",
                        "next":">"
                    }
                },
                scrollX: true,
                paging: true,
                "lengthMenu": [10,20,40,60,100,200],
                "order":[],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
            $('.table').on('dblclick', 'tr', function(){
                if($(this).attr('idx')!==undefined){
                    let link = '/client-base/'+$(this).attr('idx');
                    $('#open-show-link').attr('href', link);
                    $('#open-show-link')[0].click();   
                }
            })
        });
 
    </script>
@endsection
@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/jszip-2.5.0/dt-1.11.3/b-2.1.1/b-html5-2.1.1/b-print-2.1.1/fh-3.2.1/r-2.2.9/datatables.min.css"/>
<style>
    tr{
        cursor:pointer;
    }
</style>
@endsection 





@endsection