<?php

  namespace App\Http\Controllers\purchase;
  use Yajra\Datatables\Datatables;
  use Illuminate\Http\Request;
  use App\Http\Controllers\Controller;
  use Illuminate\Support\Facades\Log;
  use Waavi\Sanitizer\Sanitizer;
  use Illuminate\Support\Str;
  use Validator;
  use Session;
  use DB;
  use Carbon\Carbon;


  class TallyReportController extends Controller
  {
     public function index(Request $request)
    { 
        // dd("hkjhdj");
      $scope_admin = Session::get('scope_admin');

      $officecode=Session::get('officecode_admin');
       
      $adminid=Session::get('user_id_admin');

      // $office_names=DB::table('administration.officedetails')
      //               ->where('officecode','<',54)
      //               ->orderBy('officename','asc')
      //               ->pluck('officename','officecode')
      //               ->all();

      $months=DB::table('administration.months')
                    ->orderBy('id','asc')
                    ->pluck('month_val','id')
                    ->all();

      $cardtype=DB::table('consumer.consumer_cardtype')
                    ->orderBy('cardtype_id','asc')
                    ->pluck('cardtype_descrptn','cardtype_id')
                    ->all();
       
  // dd($items);
      if($adminid)
      {
        return view('purchase.tally_report',compact('office_names','months','cardtype','items'))->with('page_title','Tally Report');
      }
      else
      {
        return redirect('/admin/login');
      }

    }

 
 //////////////////////////////////////////////////////////////////////////////////


   public function ShowExcelDetails(Request $request)
    {
    
      $current_time = date("d-m-Y H:i:s");
      $officeid=$request->officeid;
      $category=$request->category;
      $month=$request->month;
      $year=$request->year;
      $type=$request->type;
      $item_id = $request->item_id;
    switch ($type) {
    case 1:
        $typename = 'Payment';
        break;
    case 2:
        $typename = 'Normal';
        break;
    case 0:
        $typename = 'ALL';
        break;
    default:
        $typename = '-';
}
 if($month && $year)
      {
          $from_date = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();

          $to_date = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

      }
     
    $plc_supp="KERALA";
    $state="KERALA";
    $country="INDIA";

 $table='<table id="table1" border="1">';
    if($category==1)//Sale
    {
 $sale_details=DB::table('stock.issuedetails')
        ->leftjoin('stock.voucherdetails','stock.voucherdetails.voucherid', 'stock.issuedetails.voucherid')
        ->leftjoin('consumer.consumerdetails','consumer.consumerdetails.consumerid','stock.issuedetails.consumer_id')
        ->leftjoin('stock.issuedetailssub','stock.issuedetailssub.issueid','stock.issuedetails.issueid')

        ->leftjoin('stock.supplyordersub','stock.supplyordersub.itemid','stock.issuedetailssub.itemid')
        ->leftjoin('stock.supplyordergeneration','stock.supplyordergeneration.supplyid','stock.supplyordersub.supplyid')

       ->leftjoin('stock.items','stock.items.itemsid','stock.supplyordersub.itemid')
       ->leftjoin('stock.units','stock.units.unitid','stock.supplyordersub.unitid')
        // ->leftjoin('stock.items','stock.items.itemsid','stock.issuedetailssub.itemid')
        // ->leftjoin('stock.global_price_list','stock.global_price_list.item_id','stock.issuedetailssub.itemid')
        // ->leftjoin('stock.global_gst','stock.global_gst.item_id','stock.issuedetailssub.itemid')
        ->leftjoin('stock.taxable_price','stock.taxable_price.card_type','consumer.consumerdetails.cardtype')
        // ->leftjoin('stock.units','stock.units.unitid','stock.issuedetailssub.unitid')

        // ->leftjoin('stock.units','stock.units.unitid','stock.issuedetailssub.unitid')
       ->where('stock.supplyordergeneration.supplydate','<=',date('Y-m-d'));


        // ->where('stock.global_price_list.effective_from','<=',date('Y-m-d'))
        // ->where('stock.global_price_list.effective_to','>=',date('Y-m-d'))
        // ->where('stock.global_gst.effective_from','<=',date('Y-m-d'))
        // ->where('stock.global_gst.effective_to','>=',date('Y-m-d'))
        // ->where('stock.taxable_price.effective_from','<=',date('Y-m-d'))
        // ->where('stock.taxable_price.effective_to','>=',date('Y-m-d'));


if (!empty($item_id)) {
    $sale_details = $sale_details->where('supplyordersub.itemid', $item_id);
}
     if($type==0) //All
      {


$sale_details= $sale_details->where('supplyordergeneration.officeid','=',$officeid)
        ->whereBetween('supplyordergeneration.supplydate',[$from_date,$to_date])

->select('supplyordergeneration.supplyid','supplyordergeneration.supplydate','voucherdetails.voucherno','voucherdetails.voucherdate','consumerdetails.cardno','consumerdetails.officename','consumerdetails.cardtype','items.itemcode','items.standard_name','items.hsn_code','supplyordersub.qty','consumerdetails.gst_no','taxable_price.percentage','units.unitname')
 ->get();
 // dd($sale_details);

        // ->select('issuedetails.issueid','issuedetails.issuedate','voucherdetails.voucherno','voucherdetails.voucherdate','consumerdetails.cardno','consumerdetails.officename','consumerdetails.cardtype','items.itemcode','items.standard_name','items.hsn_code','issuedetailssub.qty','global_price_list.price','global_price_list.gst','global_gst.cgst','global_gst.sgst','consumerdetails.gst_no','taxable_price.percentage','units.unitname')

        // ->get();



      }

////////////////////all end///////////////////////////////////////////////////



   else if($type==1) //Payment  
{
    $sale_details = $sale_details
        ->whereBetween('supplyordergeneration.supplydate', [$from_date, $to_date])
        ->select(
            'supplyordergeneration.supplyid',
            'supplyordergeneration.supplydate',
            'supplyordergeneration.supplyno',
            'voucherdetails.voucherno',
            'voucherdetails.voucherdate',
            'consumerdetails.cardno',
            'consumerdetails.officename',
            'consumerdetails.cardtype',
            'items.itemcode',
            'items.standard_name',
            'items.hsn_code',
            'supplyordersub.qty',
            'supplyordersub.cgst',
            'supplyordersub.sgst',
            'supplyordersub.igst',
            'consumerdetails.gst_no',
            'taxable_price.percentage',
            'units.unitname',
            'supplyordersub.rate'
        )
        ->get();

    // ✅ Remove duplicate rows (based on supplyid + itemcode + rate)
    $sale_details = collect($sale_details)
        ->map(function($item){ return (array)$item; }) // convert to array
        ->unique(function ($item) {
            return $item['supplyid'].'-'.$item['itemcode'].'-'.$item['rate'];
        })
        ->values()
        ->toArray();

    $cnt_sale = count($sale_details);

    $table .= '<thead>
    <tr>
        <th rowspan="2">Supply Date</th>
        <th rowspan="2">Supply Number</th>
        <th rowspan="2">Voucher Type</th>
        <th rowspan="2">Item Code</th>
        <th rowspan="2">Item Name</th>
        <th rowspan="2">HSN Code</th>
        <th rowspan="2">Unit</th>
        <th rowspan="2">Quantity</th>
        <th rowspan="2">Purchase Price</th>
        <th rowspan="2">Sale Price</th>

        <th colspan="3">GST%</th>
        <th rowspan="2">Purchase Value</th>

        <th colspan="3">ITC</th>

        <th rowspan="2">Department Charge%</th>
        <th rowspan="2">Sales Value</th>

        <th colspan="3">GST%</th>
        <th colspan="3">Cash Ledger Payment</th>
    </tr>
    <tr>
        <th>CGST %</th>
        <th>SGST %</th>
        <th>IGST %</th>

        <th>CGST Amt</th>
        <th>SGST Amt</th>
        <th>IGST Amt</th>

        <th>CGST%</th>
        <th>SGST%</th>
        <th>IGST%</th>

        <th>CGST</th>
        <th>SGST</th>
        <th>IGST</th>
    </tr>
</thead><tbody>';

    for($i=0; $i<$cnt_sale; $i++) { 
        $data = $sale_details[$i];

        $qty = $data['qty'] ?? 0;
        $purchase_price = $data['rate'] ?? 0;   // from supplyordersub.rate
        $sale_price = $data['sale_price'] ?? 0; // make sure this column exists in DB

        $cgst = $data['cgst'] ?? 0;
        $sgst = $data['sgst'] ?? 0;
        $igst = $data['igst'] ?? 0;

        $gst_percent = $cgst + $sgst + $igst;

        // 1. Purchase value
        $purchase_value = $qty * $purchase_price;

        // 2. ITC
        $itc = round($purchase_value * ($gst_percent / 100), 2);

        // 3. Credit ledger payment
        $credit_ledger_payment = round($sale_price * $qty, 2);

        // 4. Department charge (default 6% if not available)
        $department_charge = $data['department_charge'] ?? 6;

        // 5. Sale value
        $sales_value = round($sale_price * $qty * (1 + ($department_charge / 100)), 2);

        // 6. Cash ledger payment
        $cash_ledger_payment = round($sales_value - ($purchase_value * $gst_percent), 2);

        $table .= '<tr>
            <td>'.date('d-m-Y', strtotime($data['supplydate'])).'</td>
            <td>'.$data['supplyno'].'</td>
            <td>Payment</td>
            <td>'.$data['itemcode'].'</td>
            <td>'.$data['standard_name'].'</td>
            <td>'.$data['hsn_code'].'</td>
            <td>'.$data['unitname'].'</td>
            <td>'.$qty.'</td>
            <td>'.$purchase_price.'</td>
            <td>'.$sale_price.'</td>

            <td>'.$cgst.'</td>
            <td>'.$sgst.'</td>
            <td>'.$igst.'</td>

            <td>'.round($purchase_value,2).'</td>

            <td>'.round($itc,2).'</td>

            <td>'.$department_charge.'%</td>
            <td>'.round($sales_value,2).'</td>

            <td>'.$cgst.'</td>
            <td>'.$sgst.'</td>
            <td>'.$igst.'</td>

            <td>'.round($credit_ledger_payment,2).'</td>
            <td>'.round($cash_ledger_payment,2).'</td>
        </tr>';
    }





////////////////////payment end///////////////////////////////////////////////////////////////
    }
else //normal
{
    $sale_details = $sale_details
        ->whereBetween('supplyordergeneration.supplydate', [$from_date, $to_date])
        ->select(
            'supplyordergeneration.supplyid',
            'supplyordergeneration.supplydate',
            'voucherdetails.voucherno',
            'voucherdetails.voucherdate',
            'consumerdetails.cardno',
            'consumerdetails.officename',
            'consumerdetails.cardtype',
            'items.itemcode',
            'items.standard_name',
            'items.hsn_code',
            'supplyordersub.qty',
            'supplyordersub.cgst',
            'supplyordersub.sgst',
            'supplyordersub.igst',
            'consumerdetails.gst_no',
            'taxable_price.percentage',
            'units.unitname',
            'supplyordersub.rate',
            'supplyordergeneration.supplyno'
        )
        ->distinct()
        ->get();

    $sale_details = json_decode(json_encode($sale_details), true);


    $grouped = [];
    foreach ($sale_details as $row) {

        $itemcode = $row['itemcode'];
        $supplydate = $row['supplydate'];
        $supplyno = $row['supplyno'];
        $unit = $row['unitname'];

        if (!isset($grouped[$itemcode])) {

            $grouped[$itemcode] = [
                'supplydate' => $supplydate,
                'supplyno' => $supplyno,
                'itemcode' => $itemcode,
                'standard_name' => $row['standard_name'],
                'hsn_code' => $row['hsn_code'],
                'qty' => 0,
                'rate' => $row['rate'],
                'cgst' => $row['cgst'],
                'sgst' => $row['sgst'],
                'igst' => $row['igst'],
                // 'gst' => $row['gst'],
                'purchase_val' => 0,
                'cgst_amnt' => 0,
                'sgst_amnt' => 0,
                'igst_amnt' => 0,
                'total_value' => 0,
                'unitname' => $unit,
            ];

        }

        $qty = $row['qty'];
        $purchase_val = $qty * $row['rate'];
        $cgst_amnt = round($purchase_val * $row['cgst'] / 100, 2);
        $sgst_amnt = round($purchase_val * $row['sgst'] / 100, 2);
        $igst_amnt = round($purchase_val * $row['igst'] / 100, 2);
        $igst_amnt = 0; // assuming IGST is 0
        $total_value = round($purchase_val + $cgst_amnt + $sgst_amnt + $igst_amnt, 2);

        $grouped[$itemcode]['qty'] = $qty;
        $grouped[$itemcode]['purchase_val'] = $purchase_val;
        $grouped[$itemcode]['cgst_amnt'] = $cgst_amnt;
        $grouped[$itemcode]['sgst_amnt'] = $sgst_amnt;
        $grouped[$itemcode]['igst_amnt'] = $igst_amnt;
        $grouped[$itemcode]['total_value'] = $total_value;
    }

    $table .= '<thead>
        <tr>
            <th rowspan="2">Supply Date</th>
            <th rowspan="2">Supply Number</th>
            <th rowspan="2">Voucher Type</th>
            <th rowspan="2">Item Code</th>
            <th rowspan="2">Item Name</th>
            <th rowspan="2">Unit</th>
            <th rowspan="2">HSN Code</th>
            <th rowspan="2">Quantity</th>
            <th rowspan="2">Purchase Price</th>
            <th colspan="3">GST%</th>
            <th rowspan="2">Purchase Value</th>
            <th colspan="3">ITC</th>
            <th rowspan="2">Total Value</th>
        </tr>
        <tr>
            <th>CGST %</th>
            <th>SGST %</th>
            <th>IGST %</th>
            <th>CGST Amt</th>
            <th>SGST Amt</th>
            <th>IGST Amt</th>
        </tr>
    </thead><tbody>';

    // Table body
    foreach ($grouped as $item) {

         
        $table .= '<tr>
         <td>' . $item['supplydate'] . '</td> 
         <td>' . $item['supplyno'] . '</td> 
            <td>Normal</td>
           
            <td>' . $item['itemcode'] . '</td>  
            <td>' . $item['standard_name'] . '</td> 
            <td>' . $item['unitname'] . '</td> 
            <td>' . $item['hsn_code'] . '</td>
            <td>' . $item['qty'] . '</td>
            <td>' . $item['rate'] . '</td>
            <td>' . $item['cgst'] . '</td>
            <td>' . $item['sgst'] . '</td>
            <td>' . $item['igst'] . '</td>
            <td>' . round($item['purchase_val'], 2) . '</td>
            <td>' . round($item['cgst_amnt'], 2) . '</td>
            <td>' . round($item['sgst_amnt'], 2) . '</td>
            <td>' . round($item['igst_amnt'], 2) . '</td>
            <td>' . round($item['total_value'], 2) . '</td>
        </tr>';
    }

    $table .= '</tbody>';
}

//////normal end///////////////////////////////////////////////////////////////



    }
    $table .= '</table>';

$file_no = 'Sale and Purchase ' . $current_time;
$array_val = array($table, $file_no);

return $array_val;
    }

}

// }