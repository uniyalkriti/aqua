@if(!empty($records))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o "></i> Export Excel</a>
@endif
<table id="simple-table" class="table table-bordered" style="font-size: 10px">
    <thead>
    <tr>
        <th rowspan="3">TOWN / BELT / TERRITORY / AREA / REGION/ ZONE</th>
        <th rowspan="3">DISTRIBUTOR/ISR/SO/ ASM / RSM TO ZONAL HEAD</th>
        <th rowspan="3">PRODUCT</th>
        <th colspan="4">PREVIOUS MONTH STOCK STATEMENT IN RV</th>
        <th rowspan="3">ACH JUL'17</th>
        <th rowspan="3">ACH JUN'18</th>
        <th rowspan="3">TARGET JUL'18</th>
        <th colspan="39">DATE WISE UCDP PLAN AS PER TARGET (RV LACS)</th>
        <th rowspan="3">Total</th>
        <th rowspan="3">COMMITTED S.S.+ I.D. DURING MONTH</th>
        <th rowspan="3">ESTIMATED CLOSING STOCK ON THE LAST DAY OF MONTH</th>
    </tr>
    <tr>
        <th rowspan="2">OPENING STOCK 1ST OF LAST MONTH</th>
        <th rowspan="2">PRIMARY SALES DURING PREVIOUS MONTH</th>
        <th rowspan="2">SECONDARY + ID DURING PREVIOUS MONTH</th>
        <th rowspan="2">OPENING STOCK AS ON 1ST OF THIS MONTH</th>
    </tr>
        <tr>
            <th>1</th>
            <th>2</th>
            <th>3</th>
            <th>4</th>
            <th>5</th>
            <th>6</th>
            <th>7</th>
            <th>Total 1-7</th>
            <th>8</th>
            <th>9</th>
            <th>10</th>
            <th>11</th>
            <th>12</th>
            <th>13</th>
            <th>14</th>
            <th>Total 8-14</th>
            <th>Total 1-14</th>
            <th>14</th>
            <th>15</th>
            <th>16</th>
            <th>17</th>
            <th>18</th>
            <th>19</th>
            <th>20</th>
            <th>21</th>
            <th>Total 15-21</th>
            <th>Total 1-21</th>
            <th>22</th>
            <th>23</th>
            <th>24</th>
            <th>25</th>
            <th>26</th>
            <th>27</th>
            <th>28</th>
            <th>Total 22-28</th>
            <th>Total 1-28</th>
            <th>29</th>
            <th>30</th>
            <th>31</th>
        </tr>
    </thead>
    <tbody>
    @if(!empty($records) && count($records)>0))
        <tr>
            <td>OPENING STOCK 1ST OF LAST MONTH</td>
            <td>PRIMARY SALES DURING PREVIOUS MONTH</td>
            <td>SECONDARY + ID DURING PREVIOUS MONTH</td>
            <td>OPENING STOCK AS ON 1ST OF THIS MONTH</td>
        </tr>
    @else
        <tr>
            <td colspan="52">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>