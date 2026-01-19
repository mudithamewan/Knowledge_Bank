<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\reqested_item;
use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Models\ValidationModel;
use App\Models\CommonModel;
use App\Models\UserModel;
use App\Models\StockModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\VarDumper\VarDumper;

class ProductController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function Category_Medium()
    {
        return view('Products/Category_Medium');
    }

    public function get_medium_result_table(Request $request)
    {
        $DOWNLOAD = trim($request->input('DOWNLOAD'));

        $ProductModel = new ProductModel();
        $result = $ProductModel->get_mediums();

        if ($DOWNLOAD == 'YES') {
            $view = (string)view('Products/Download/Medium_Download', [
                'result' => $result,
            ]);
            header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=Medium List Download.xls");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            echo $view;
        } else {
            if ($request->ajax()) {
                return datatables()->of($result)->toJson();
            }
        }
    }

    public function save_new_medium(Request $request)
    {
        $ValidationModel = new ValidationModel();
        $CommonModel = new CommonModel();

        $error = "";
        $NAME = trim($request->input('NAME'));
        if ($ValidationModel->is_invalid_data($NAME)) {
            $error .= "- Name cannot be empty<br>";
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            DB::beginTransaction();
            try {

                $data1 = array(
                    'mm_status' => 1,
                    'mm_inserted_date' => date('Y-m-d H:i:s'),
                    'mm_inserted_by' => session('USER_ID'),
                    'mm_name' => ucwords($NAME),
                    'mm_is_active' => 1
                );
                $MEDIUM_ID = DB::table('master_medium')->insertGetId($data1);

                $CommonModel->update_work_log(session('USER_ID'), 'New Medium has been created. Medium ID:' . $MEDIUM_ID);

                DB::commit();
                return json_encode(array('success' => 'New Medium has been created.'));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function load_edit_medium_view(Request $request)
    {
        $ValidationModel = new ValidationModel();

        $error = "";
        $DATA = json_decode(trim($request->input('DATA')));
        $MEDIUM_ID = $DATA->MEDIUM_ID;
        if ($ValidationModel->is_invalid_data($MEDIUM_ID)) {
            $error .= "- Something went wrong!<br>";
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            $ProductModel = new ProductModel();
            $MEDIUM_DETAILS = $ProductModel->get_medium_details_by_id($MEDIUM_ID);

            $view = (string)view('Products/Load_Edit_Medium_View', [
                'MEDIUM_ID' => $MEDIUM_ID,
                'MEDIUM_DETAILS' => $MEDIUM_DETAILS,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function update_medium(Request $request)
    {
        $ValidationModel = new ValidationModel();

        $error = "";
        $MM_ID = trim($request->input('MM_ID'));
        $NAME = trim($request->input('NAME'));
        $STATUS = trim($request->input('STATUS'));

        if ($ValidationModel->is_invalid_data($NAME)) {
            $error .= "- Name cannot be empty.<br>";
        }
        if ($STATUS == '1' || $STATUS == '0') {
        } else {
            $error .= "- Invalid Status Code.<br>";
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            DB::beginTransaction();
            try {
                $CommonModel = new CommonModel();

                $data1 = array(
                    'mm_updated_date' => date('Y-m-d H:i:s'),
                    'mm_updated_by' => session('USER_ID'),
                    'mm_is_active' => $STATUS,
                    'mm_name' => ucwords($NAME),
                );
                DB::table('master_medium')
                    ->where('mm_id', $MM_ID)
                    ->update($data1);

                $CommonModel->update_work_log(session('USER_ID'), 'Medium details has been updated. Medium ID:' . $MM_ID);

                DB::commit();
                return json_encode(array('success' => 'Medium details has been updated.'));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function Category_Grades()
    {
        return view('Products/Category_Grades');
    }

    public function get_grade_result_table(Request $request)
    {
        $DOWNLOAD = trim($request->input('DOWNLOAD'));

        $ProductModel = new ProductModel();
        $result = $ProductModel->get_gades();

        if ($DOWNLOAD == 'YES') {
            $view = (string)view('Products/Download/Grade_Download', [
                'result' => $result,
            ]);
            header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=Grade List Download.xls");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            echo $view;
        } else {
            if ($request->ajax()) {
                return datatables()->of($result)->toJson();
            }
        }
    }

    public function save_new_grade(Request $request)
    {
        $ValidationModel = new ValidationModel();
        $CommonModel = new CommonModel();

        $error = "";
        $NAME = trim($request->input('NAME'));
        if ($ValidationModel->is_invalid_data($NAME)) {
            $error .= "- Grade cannot be empty<br>";
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            DB::beginTransaction();
            try {

                $data1 = array(
                    'mg_status' => 1,
                    'mg_inserted_date' => date('Y-m-d H:i:s'),
                    'mg_inserted_by' => session('USER_ID'),
                    'mg_name' => ucwords($NAME),
                    'mg_is_active' => 1
                );
                $GRADE_ID = DB::table('master_grades')->insertGetId($data1);

                $CommonModel->update_work_log(session('USER_ID'), 'New Grade has been created. Grade ID:' . $GRADE_ID);

                DB::commit();
                return json_encode(array('success' => 'New Grade has been created.'));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function load_edit_grade_view(Request $request)
    {
        $ValidationModel = new ValidationModel();

        $error = "";
        $DATA = json_decode(trim($request->input('DATA')));
        $GRADE_ID = $DATA->GRADE_ID;
        if ($ValidationModel->is_invalid_data($GRADE_ID)) {
            $error .= "- Something went wrong!<br>";
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            $ProductModel = new ProductModel();
            $GRADE_DETAILS = $ProductModel->get_grade_details_by_id($GRADE_ID);

            $view = (string)view('Products/Load_Edit_Grade_View', [
                'GRADE_ID' => $GRADE_ID,
                'GRADE_DETAILS' => $GRADE_DETAILS,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function update_grade(Request $request)
    {
        $ValidationModel = new ValidationModel();

        $error = "";
        $MG_ID = trim($request->input('MG_ID'));
        $NAME = trim($request->input('NAME'));
        $STATUS = trim($request->input('STATUS'));

        if ($ValidationModel->is_invalid_data($NAME)) {
            $error .= "- Name cannot be empty.<br>";
        }
        if ($STATUS == '1' || $STATUS == '0') {
        } else {
            $error .= "- Invalid Status Code.<br>";
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            DB::beginTransaction();
            try {
                $CommonModel = new CommonModel();

                $data1 = array(
                    'mg_updated_date' => date('Y-m-d H:i:s'),
                    'mg_updated_by' => session('USER_ID'),
                    'mg_is_active' => $STATUS,
                    'mg_name' => ucwords($NAME),
                );
                DB::table('master_grades')
                    ->where('mg_id', $MG_ID)
                    ->update($data1);

                $CommonModel->update_work_log(session('USER_ID'), 'Grade details has been updated. Grade ID:' . $MG_ID);

                DB::commit();
                return json_encode(array('success' => 'Grade details has been updated.'));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function Category_Subjects()
    {
        return view('Products/Category_Subjects');
    }

    public function get_subject_result_table(Request $request)
    {
        $DOWNLOAD = trim($request->input('DOWNLOAD'));

        $ProductModel = new ProductModel();
        $result = $ProductModel->get_subjects();

        if ($DOWNLOAD == 'YES') {
            $view = (string)view('Products/Download/Subject_Download', [
                'result' => $result,
            ]);
            header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=Subject List Download.xls");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            echo $view;
        } else {
            if ($request->ajax()) {
                return datatables()->of($result)->toJson();
            }
        }
    }


    public function save_new_subject(Request $request)
    {
        $ValidationModel = new ValidationModel();
        $CommonModel = new CommonModel();

        $error = "";
        $NAME = trim($request->input('NAME'));
        if ($ValidationModel->is_invalid_data($NAME)) {
            $error .= "- Subject cannot be empty<br>";
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            DB::beginTransaction();
            try {

                $data1 = array(
                    'ms_status' => 1,
                    'ms_inserted_date' => date('Y-m-d H:i:s'),
                    'ms_inserted_by' => session('USER_ID'),
                    'ms_name' => ucwords($NAME),
                    'ms_is_active' => 1
                );
                $SUBJECT_ID = DB::table('master_subjects')->insertGetId($data1);

                $CommonModel->update_work_log(session('USER_ID'), 'New Subject has been created. Subject ID:' . $SUBJECT_ID);

                DB::commit();
                return json_encode(array('success' => 'New Subject has been created.'));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function load_edit_subject_view(Request $request)
    {
        $ValidationModel = new ValidationModel();

        $error = "";
        $DATA = json_decode(trim($request->input('DATA')));
        $SUBJECT_ID = $DATA->SUBJECT_ID;
        if ($ValidationModel->is_invalid_data($SUBJECT_ID)) {
            $error .= "- Something went wrong!<br>";
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            $ProductModel = new ProductModel();
            $SUBJECT_DETAILS = $ProductModel->get_subject_details_by_id($SUBJECT_ID);

            $view = (string)view('Products/Load_Edit_Subject_View', [
                'SUBJECT_ID' => $SUBJECT_ID,
                'SUBJECT_DETAILS' => $SUBJECT_DETAILS,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function update_subject(Request $request)
    {
        $ValidationModel = new ValidationModel();

        $error = "";
        $MS_ID = trim($request->input('MS_ID'));
        $NAME = trim($request->input('NAME'));
        $STATUS = trim($request->input('STATUS'));

        if ($ValidationModel->is_invalid_data($NAME)) {
            $error .= "- Subject cannot be empty.<br>";
        }
        if ($STATUS == '1' || $STATUS == '0') {
        } else {
            $error .= "- Invalid Status Code.<br>";
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            DB::beginTransaction();
            try {
                $CommonModel = new CommonModel();

                $data1 = array(
                    'ms_updated_date' => date('Y-m-d H:i:s'),
                    'ms_updated_by' => session('USER_ID'),
                    'ms_is_active' => $STATUS,
                    'ms_name' => ucwords($NAME),
                );
                DB::table('master_subjects')
                    ->where('ms_id', $MS_ID)
                    ->update($data1);

                $CommonModel->update_work_log(session('USER_ID'), 'Subject details has been updated. Grade ID:' . $MS_ID);

                DB::commit();
                return json_encode(array('success' => 'Subject details has been updated.'));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function Category()
    {
        return view('Products/Category');
    }

    public function save_new_category(Request $request)
    {
        $ValidationModel = new ValidationModel();
        $CommonModel = new CommonModel();

        $error = "";
        $NAME = trim($request->input('NAME'));
        $DESCRIPTION = trim($request->input('DESCRIPTION'));
        if ($ValidationModel->is_invalid_data($NAME)) {
            $error .= "- Subject cannot be empty<br>";
        }

        if ($ValidationModel->is_invalid_data($DESCRIPTION)) {
        } else if (strlen($DESCRIPTION) > 255) {
            $error .= "- Description cannot exceed 255 characters<br>";
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            DB::beginTransaction();
            try {

                $data1 = array(
                    'mc_status' => 1,
                    'mc_inserted_date' => date('Y-m-d H:i:s'),
                    'mc_inserted_by' => session('USER_ID'),
                    'mc_name' => ucwords($NAME),
                    'mc_description' => $DESCRIPTION,
                    'mc_is_active' => 1
                );
                $CATE_ID = DB::table('master_categories')->insertGetId($data1);

                $CommonModel->update_work_log(session('USER_ID'), 'New Category has been created. Category ID:' . $CATE_ID);

                DB::commit();
                return json_encode(array('success' => 'New Category has been created.'));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function get_category_result_table(Request $request)
    {
        $DOWNLOAD = trim($request->input('DOWNLOAD'));

        $ProductModel = new ProductModel();
        $result = $ProductModel->get_categories();

        if ($DOWNLOAD == 'YES') {
            $view = (string)view('Products/Download/Category_Download', [
                'result' => $result,
            ]);
            header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=Category List Download.xls");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            echo $view;
        } else {
            if ($request->ajax()) {
                return datatables()->of($result)->toJson();
            }
        }
    }

    public function load_edit_category_view(Request $request)
    {
        $ValidationModel = new ValidationModel();

        $error = "";
        $DATA = json_decode(trim($request->input('DATA')));
        $CATE_ID = $DATA->CATE_ID;
        if ($ValidationModel->is_invalid_data($CATE_ID)) {
            $error .= "- Something went wrong!<br>";
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            $ProductModel = new ProductModel();
            $CATEGORY_DETAILS = $ProductModel->get_category_details_by_id($CATE_ID);

            $view = (string)view('Products/Load_Edit_Category_View', [
                'CATE_ID' => $CATE_ID,
                'CATEGORY_DETAILS' => $CATEGORY_DETAILS,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function update_category(Request $request)
    {
        $ValidationModel = new ValidationModel();

        $error = "";
        $MC_ID = trim($request->input('MC_ID'));
        $NAME = trim($request->input('NAME'));
        $STATUS = trim($request->input('STATUS'));
        $DESCRIPTION = trim($request->input('DESCRIPTION'));

        if ($ValidationModel->is_invalid_data($NAME)) {
            $error .= "- Category cannot be empty.<br>";
        }
        if ($ValidationModel->is_invalid_data($DESCRIPTION)) {
        } else if (strlen($DESCRIPTION) > 255) {
            $error .= "- Description cannot exceed 255 characters<br>";
        }
        if ($STATUS == '1' || $STATUS == '0') {
        } else {
            $error .= "- Invalid Status Code.<br>";
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            DB::beginTransaction();
            try {
                $CommonModel = new CommonModel();

                $data1 = array(
                    'mc_updated_date' => date('Y-m-d H:i:s'),
                    'mc_updated_by' => session('USER_ID'),
                    'mc_is_active' => $STATUS,
                    'mc_name' => ucwords($NAME),
                    'mc_description' => $DESCRIPTION,
                );
                DB::table('master_categories')
                    ->where('mc_id', $MC_ID)
                    ->update($data1);

                $CommonModel->update_work_log(session('USER_ID'), 'Category details has been updated. Category ID:' . $MC_ID);

                DB::commit();
                return json_encode(array('success' => 'Category details has been updated.'));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function Category_Sub_Category()
    {
        $ProductModel = new ProductModel();

        $CATEGORIES = $ProductModel->get_active_categories();
        return view('Products/Category_Sub_Category', [
            'CATEGORIES' => $CATEGORIES
        ]);
    }

    public function get_sub_category_result_table(Request $request)
    {
        $DOWNLOAD = trim($request->input('DOWNLOAD'));

        $ProductModel = new ProductModel();
        $result = $ProductModel->get_sub_categories();

        if ($DOWNLOAD == 'YES') {
            $view = (string)view('Products/Download/Sub_Category_Download', [
                'result' => $result,
            ]);
            header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=Sub Category List Download.xls");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            echo $view;
        } else {
            if ($request->ajax()) {
                return datatables()->of($result)->toJson();
            }
        }
    }

    public function save_new_sub_category(Request $request)
    {
        $ValidationModel = new ValidationModel();
        $CommonModel = new CommonModel();

        $error = "";
        $NAME = trim($request->input('NAME'));
        $DESCRIPTION = trim($request->input('DESCRIPTION'));
        $MC_ID = trim($request->input('MC_ID'));

        if ($ValidationModel->is_invalid_data($NAME)) {
            $error .= "- Subject cannot be empty<br>";
        }

        if ($ValidationModel->is_invalid_data($MC_ID)) {
            $error .= "- Category not selected.<br>";
        }

        if ($ValidationModel->is_invalid_data($DESCRIPTION)) {
        } else if (strlen($DESCRIPTION) > 255) {
            $error .= "- Description cannot exceed 255 characters<br>";
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            DB::beginTransaction();
            try {

                $data1 = array(
                    'msc_status' => 1,
                    'msc_inserted_date' => date('Y-m-d H:i:s'),
                    'msc_inserted_by' => session('USER_ID'),
                    'msc_mc_id' => $MC_ID,
                    'msc_name' => ucwords($NAME),
                    'msc_description' => $DESCRIPTION,
                    'msc_is_active' => 1
                );
                $SUB_CATE_ID = DB::table('master_sub_categories')->insertGetId($data1);

                $CommonModel->update_work_log(session('USER_ID'), 'New Sub Category has been created. Sub Category ID:' . $SUB_CATE_ID);

                DB::commit();
                return json_encode(array('success' => 'New Sub Category has been created.'));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function load_edit_sub_category_view(Request $request)
    {
        $ValidationModel = new ValidationModel();

        $error = "";
        $DATA = json_decode(trim($request->input('DATA')));
        $SUB_CATE_ID = $DATA->SUB_CATE_ID;
        if ($ValidationModel->is_invalid_data($SUB_CATE_ID)) {
            $error .= "- Something went wrong!<br>";
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            $ProductModel = new ProductModel();

            $CATEGORIES = $ProductModel->get_active_categories();
            $SUB_CATEGORY_DETAILS = $ProductModel->get_sub_category_details_by_id($SUB_CATE_ID);

            $view = (string)view('Products/Load_Edit_Sub_Category_View', [
                'SUB_CATE_ID' => $SUB_CATE_ID,
                'SUB_CATEGORY_DETAILS' => $SUB_CATEGORY_DETAILS,
                'CATEGORIES' => $CATEGORIES,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function update_sub_category(Request $request)
    {
        $ValidationModel = new ValidationModel();

        $error = "";
        $MSC_ID = trim($request->input('MSC_ID'));
        $NAME = trim($request->input('NAME'));
        $STATUS = trim($request->input('STATUS'));
        $DESCRIPTION = trim($request->input('DESCRIPTION'));
        $MC_ID = trim($request->input('MC_ID'));

        if ($ValidationModel->is_invalid_data($NAME)) {
            $error .= "- Category cannot be empty.<br>";
        }
        if ($ValidationModel->is_invalid_data($DESCRIPTION)) {
            // $error .= "- Description cannot be empty.<br>";
        } else if (strlen($DESCRIPTION) > 255) {
            $error .= "- Description cannot exceed 255 characters<br>";
        }
        if ($STATUS == '1' || $STATUS == '0') {
        } else {
            $error .= "- Invalid Status Code.<br>";
        }
        if ($ValidationModel->is_invalid_data($MC_ID)) {
            $error .= "- Category not selected.<br>";
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            DB::beginTransaction();
            try {
                $CommonModel = new CommonModel();

                $data1 = array(
                    'msc_updated_date' => date('Y-m-d H:i:s'),
                    'msc_updated_by' => session('USER_ID'),
                    'msc_is_active' => $STATUS,
                    'msc_mc_id' => $MC_ID,
                    'msc_name' => ucwords($NAME),
                    'msc_description' => $DESCRIPTION,
                );
                DB::table('master_sub_categories')
                    ->where('msc_id', $MSC_ID)
                    ->update($data1);

                $CommonModel->update_work_log(session('USER_ID'), 'Sub Category details has been updated. Sub Category ID:' . $MSC_ID);

                DB::commit();
                return json_encode(array('success' => 'Sub Category details has been updated.'));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function Add_New_Product()
    {
        $ProductModel = new ProductModel();
        $UserModel = new UserModel();

        $MEDIUMS = $ProductModel->get_active_mediums();
        $GRADES = $ProductModel->get_active_gades();
        $SUBJECTS = $ProductModel->get_active_subjects();
        $CATEGORIES = $ProductModel->get_active_categories();
        $PUBLISHERS = $UserModel->get_publishers();
        $BOOK_FORMATS = $ProductModel->get_book_formats();

        return view('Products/Add_New_Product', [
            'MEDIUMS' => $MEDIUMS,
            'GRADES' => $GRADES,
            'SUBJECTS' => $SUBJECTS,
            'CATEGORIES' => $CATEGORIES,
            'PUBLISHERS' => $PUBLISHERS,
            'BOOK_FORMATS' => $BOOK_FORMATS,
        ]);
    }

    public function get_sub_categories($MM_ID)
    {
        $ProductModel = new ProductModel();
        $search = $_GET['q'];
        $content = array();

        $SUB_CATEGORIES =  $ProductModel->get_sub_category_by_search($search, $MM_ID);

        foreach ($SUB_CATEGORIES as $data) {
            $data = array(
                "msc_id" => $data->msc_id,
                "msc_name" => strtoupper($data->msc_name)
            );
            array_push($content, $data);
        }

        header('Content-Type: application/json');
        echo json_encode(
            $content
        );
    }

    public function validate_save_product($request)
    {
        $error = "";
        $ValidationModel = new ValidationModel();

        $NAME = trim($request->input('NAME'));
        $ISBN = trim($request->input('ISBN'));
        $AUTHOR = trim($request->input('AUTHOR'));
        $PUBLISHER_ID = trim($request->input('PUBLISHER_ID'));
        $MM_ID = trim($request->input('MM_ID'));
        $MG_ID = $request->input('MG_ID');
        $MS_ID = $request->input('MS_ID');
        $MC_ID = trim($request->input('MC_ID'));
        $MSC_ID = trim($request->input('MSC_ID'));
        $DESCRIPTION = trim($request->input('DESCRIPTION'));

        if ($ValidationModel->is_invalid_data($NAME)) {
            $error .= "- Product Name cannot be empty<br>";
        }
        if ($ValidationModel->is_invalid_data($ISBN)) {
            $error .= "- ISBN cannot be empty<br>";
        }

        if ($ValidationModel->is_invalid_data($PUBLISHER_ID)) {
            $error .= "- Publisher cannot be empty<br>";
        }
        if ($ValidationModel->is_invalid_data($MM_ID)) {
            $error .= "- Medium cannot be empty<br>";
        }
        if (empty($MG_ID)) {
            $error .= "- Grades cannot be empty<br>";
        }
        if (empty($MS_ID)) {
            $error .= "- Subjects cannot be empty<br>";
        }
        if ($ValidationModel->is_invalid_data($MC_ID)) {
            $error .= "- Category cannot be empty<br>";
        }
        if ($ValidationModel->is_invalid_data($MSC_ID)) {
            $error .= "- Sub Category cannot be empty<br>";
        }

        if ($request->hasFile('COVER_PAGE')) {
            $file = $request->file('COVER_PAGE');
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $extension = strtolower($file->getClientOriginalExtension());

            if (!in_array($extension, $allowedExtensions)) {
                $error .= "- Invalid file type. Only JPG, JPEG, PNG, GIF allowed.<br>";
            }
            if ($file->getSize() > 5 * 1024 * 1024) {
                $error .= "- File size must not exceed 5 MB.<br>";
            }
        }

        return $error;
    }

    public function save_product(Request $request)
    {
        $CommonModel = new CommonModel();
        $ValidationModel = new ValidationModel();
        $status = $this->validate_save_product($request, true);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {

            $NAME = trim($request->input('NAME'));
            $ISBN = trim($request->input('ISBN'));
            $AUTHOR = trim($request->input('AUTHOR'));

            if ($ValidationModel->is_invalid_data($AUTHOR)) {
                $AUTHOR = 'N/A';
            }

            $PUBLISHER_ID = trim($request->input('PUBLISHER_ID'));
            $MM_ID = trim($request->input('MM_ID'));
            $MG_ID = $request->input('MG_ID');
            $MS_ID = $request->input('MS_ID');
            $MC_ID = trim($request->input('MC_ID'));
            $MSC_ID = trim($request->input('MSC_ID'));
            $DESCRIPTION = trim($request->input('DESCRIPTION'));
            $EDITION = trim($request->input('EDITION'));
            $PUBLICATION_YEAR = trim($request->input('PUBLICATION_YEAR'));
            $PAGE_COUNT = trim($request->input('PAGE_COUNT'));
            $FORMAT = trim($request->input('FORMAT'));


            DB::beginTransaction();
            try {

                $data1 = array(
                    'p_status' => 1,
                    'p_inserted_date' => date('Y-m-d H:i:s'),
                    'p_inserted_by' => session('USER_ID'),
                    'p_is_active' => 1,
                    'p_name' => $NAME,
                    'p_isbn' => $ISBN,
                    'p_author' => $AUTHOR,
                    'p_publisher_id' => $PUBLISHER_ID,
                    'p_mm_id' => $MM_ID,
                    'p_mc_id' => $MC_ID,
                    'p_msc_id' => $MSC_ID,
                    'p_description' => $DESCRIPTION,
                    'p_edition' => $EDITION,
                    'p_published_year' => $PUBLICATION_YEAR,
                    'p_page_count' => $PAGE_COUNT,
                    'p_mbf_id' => $FORMAT,
                );
                $PRODUCT_ID = DB::table('products')->insertGetId($data1);

                foreach ($MG_ID as $grade) {
                    $data2 = array(
                        'pg_status' => 1,
                        'pg_inserted_date' => date('Y-m-d H:i:s'),
                        'pg_inserted_by' => session('USER_ID'),
                        'pg_p_id' => $PRODUCT_ID,
                        'pg_mg_id' => $grade,
                    );
                    DB::table('product_grades')->insert($data2);
                }

                foreach ($MS_ID as $subject) {
                    $data3 = array(
                        'ps_status' => 1,
                        'ps_inserted_date' => date('Y-m-d H:i:s'),
                        'ps_inserted_by' => session('USER_ID'),
                        'ps_p_id' => $PRODUCT_ID,
                        'ps_ms_id' => $subject,
                    );
                    DB::table('product_subjects')->insert($data3);
                }

                if ($request->hasFile('COVER_PAGE')) {
                    $file = $request->file('COVER_PAGE');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $folderPath = 'Products/' . $PRODUCT_ID;
                    $filePath   = $folderPath . '/' . $filename;

                    $file->move(public_path($folderPath), $filename);

                    $data4 = [
                        'pd_status' => 1,
                        'pd_inserted_date' => now(),
                        'pd_inserted_by' => session('USER_ID'),
                        'pd_p_id' => $PRODUCT_ID,
                        'pd_file_name' => $filename,
                        'pd_original_name' => $file->getClientOriginalName(),
                        'pd_file_path' => $filePath,
                    ];
                    DB::table('product_documents')->insert($data4);
                }

                $CommonModel->update_work_log(session('USER_ID'), 'New Product has been created. Product ID:' . $PRODUCT_ID);

                DB::commit();
                return json_encode(array('success' => 'New Product has been created.', 'p_id' => $PRODUCT_ID));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function Product_Profile($PRODUCT_ID)
    {
        $ProductModel = new ProductModel();

        $PRODUCT_ID = base64_decode($PRODUCT_ID);

        $PRODUCT_DETAILS = $ProductModel->get_product_details($PRODUCT_ID);
        $PRODUCT_GRADES = $ProductModel->get_product_grades($PRODUCT_ID);
        $PRODUCT_SUBJECTS = $ProductModel->get_product_subjects($PRODUCT_ID);
        $PRODUCT_AVA_COUNT = $ProductModel->get_product_available_count($PRODUCT_ID);

        $TODAY_STOCK_IN_COUNT = $ProductModel->get_product_in_list_count($PRODUCT_ID, date('Y-m-d'));
        $TODAY_STOCK_OUT_COUNT = $ProductModel->get_product_out_list_count($PRODUCT_ID, date('Y-m-d'));

        return view('Products/Product_Profile', [
            'PRODUCT_ID' => $PRODUCT_ID,
            'PRODUCT_DETAILS' => $PRODUCT_DETAILS,
            'PRODUCT_GRADES' => $PRODUCT_GRADES,
            'PRODUCT_SUBJECTS' => $PRODUCT_SUBJECTS,
            'TODAY_STOCK_IN_COUNT' => $TODAY_STOCK_IN_COUNT,
            'TODAY_STOCK_OUT_COUNT' => $TODAY_STOCK_OUT_COUNT,
            'PRODUCT_AVA_COUNT' => $PRODUCT_AVA_COUNT,
        ]);
    }

    public function load_edit_product_profile_view(Request $request)
    {
        $ProductModel = new ProductModel();
        $UserModel = new UserModel();

        $DATA = json_decode(trim($request->input('DATA')));

        $PRODUCT_ID = $DATA->PRODUCT_ID;
        $PRODUCT_DETAILS = $ProductModel->get_product_details($PRODUCT_ID);
        $PRODUCT_GRADES = $ProductModel->get_product_grades($PRODUCT_ID);
        $PRODUCT_SUBJECTS = $ProductModel->get_product_subjects($PRODUCT_ID);

        $PRODUCT_GRADES_IDS = array();
        foreach ($PRODUCT_GRADES as $DATA) {
            array_push($PRODUCT_GRADES_IDS, $DATA->mg_id);
        }

        $PRODUCT_SUBJECTS_IDS = array();
        foreach ($PRODUCT_SUBJECTS as $DATA) {
            array_push($PRODUCT_SUBJECTS_IDS, $DATA->ms_id);
        }

        $MEDIUMS = $ProductModel->get_active_mediums();
        $GRADES = $ProductModel->get_active_gades();
        $SUBJECTS = $ProductModel->get_active_subjects();
        $CATEGORIES = $ProductModel->get_active_categories();
        $PUBLISHERS = $UserModel->get_publishers();
        $BOOK_FORMATS = $ProductModel->get_book_formats();

        $view = (string)view('Products/Load_Edit_Product_Profile_View', [
            'PRODUCT_ID' => $PRODUCT_ID,
            'PRODUCT_DETAILS' => $PRODUCT_DETAILS,
            'PRODUCT_GRADES' => $PRODUCT_GRADES,
            'PRODUCT_SUBJECTS' => $PRODUCT_SUBJECTS,
            'PRODUCT_GRADES_IDS' => $PRODUCT_GRADES_IDS,
            'PRODUCT_SUBJECTS_IDS' => $PRODUCT_SUBJECTS_IDS,
            'MEDIUMS' => $MEDIUMS,
            'GRADES' => $GRADES,
            'SUBJECTS' => $SUBJECTS,
            'CATEGORIES' => $CATEGORIES,
            'PUBLISHERS' => $PUBLISHERS,
            'BOOK_FORMATS' => $BOOK_FORMATS,
        ]);
        return json_encode(array('result' => $view));
    }

    public function update_product(Request $request)
    {
        $CommonModel = new CommonModel();
        $ValidationModel = new ValidationModel();
        $status = $this->validate_save_product($request, true);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $P_ID = trim($request->input('P_ID'));
            $ACTIVE_STATUS = trim($request->input('ACTIVE_STATUS'));
            $NAME = trim($request->input('NAME'));
            $ISBN = trim($request->input('ISBN'));

            $AUTHOR = trim($request->input('AUTHOR'));
            if ($ValidationModel->is_invalid_data($AUTHOR)) {
                $AUTHOR = 'N/A';
            }

            $PUBLISHER_ID = trim($request->input('PUBLISHER_ID'));
            $MM_ID = trim($request->input('MM_ID'));
            $MG_ID = $request->input('MG_ID');
            $MS_ID = $request->input('MS_ID');
            $MC_ID = trim($request->input('MC_ID'));
            $MSC_ID = trim($request->input('MSC_ID'));
            $DESCRIPTION = trim($request->input('DESCRIPTION'));
            $EDITION = trim($request->input('EDITION'));
            $PUBLICATION_YEAR = trim($request->input('PUBLICATION_YEAR'));
            $PAGE_COUNT = trim($request->input('PAGE_COUNT'));
            $FORMAT = trim($request->input('FORMAT'));

            DB::beginTransaction();
            try {
                $data1 = array(
                    'p_status' => 1,
                    'p_updated_date' => date('Y-m-d H:i:s'),
                    'p_updated_by' => session('USER_ID'),
                    'p_is_active' => $ACTIVE_STATUS == 'ACTIVE' ? 1 : 0,
                    'p_name' => ucwords($NAME),
                    'p_isbn' => $ISBN,
                    'p_author' => ucwords($AUTHOR),
                    'p_publisher_id' => $PUBLISHER_ID,
                    'p_mm_id' => $MM_ID,
                    'p_mc_id' => $MC_ID,
                    'p_msc_id' => $MSC_ID,
                    'p_description' => $DESCRIPTION,
                    'p_edition' => $EDITION,
                    'p_published_year' => $PUBLICATION_YEAR,
                    'p_page_count' => $PAGE_COUNT,
                    'p_mbf_id' => $FORMAT,
                );
                DB::table('products')
                    ->where('p_id', $P_ID)
                    ->update($data1);


                $data2 = array(
                    'pg_status' => 0,
                    'pg_updated_date' => date('Y-m-d H:i:s'),
                    'pg_updated_by' => session('USER_ID'),
                );
                DB::table('product_grades')
                    ->where('pg_p_id', $P_ID)
                    ->update($data2);

                foreach ($MG_ID as $grade) {
                    $data3 = array(
                        'pg_status' => 1,
                        'pg_inserted_date' => date('Y-m-d H:i:s'),
                        'pg_inserted_by' => session('USER_ID'),
                        'pg_p_id' => $P_ID,
                        'pg_mg_id' => $grade,
                    );
                    DB::table('product_grades')->insert($data3);
                }

                $data4 = array(
                    'ps_status' => 0,
                    'ps_updated_date' => date('Y-m-d H:i:s'),
                    'ps_updated_by' => session('USER_ID'),
                );
                DB::table('product_subjects')
                    ->where('ps_p_id', $P_ID)
                    ->update($data4);

                foreach ($MS_ID as $subject) {
                    $data5 = array(
                        'ps_status' => 1,
                        'ps_inserted_date' => date('Y-m-d H:i:s'),
                        'ps_inserted_by' => session('USER_ID'),
                        'ps_p_id' => $P_ID,
                        'ps_ms_id' => $subject,
                    );
                    DB::table('product_subjects')->insert($data5);
                }


                $CommonModel->update_work_log(session('USER_ID'), 'Product has been updated. Product ID:' . $P_ID);

                DB::commit();
                return json_encode(array('success' => 'Product has been updated.', 'p_id' => $P_ID));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function Manage_Products()
    {
        return view('Products/Manage_Products');
    }

    public function get_product_filter_result_result($request)
    {
        $ValidationModel = new ValidationModel();
        $error = "";

        $FROM_DATE = trim($request->input('FROM_DATE'));
        $TO_DATE = trim($request->input('TO_DATE'));
        $P_ID = trim($request->input('P_ID'));

        if ($ValidationModel->is_invalid_data($P_ID)) {
            $is_set_from_date = true;
            $is_set_to_date = true;

            if ($ValidationModel->is_invalid_data($FROM_DATE)) {
                $is_set_from_date = false;
                $error .= "- From Date cannot be empty<br>";
            }
            if ($ValidationModel->is_invalid_data($TO_DATE)) {
                $is_set_to_date = false;
                $error .= "- To Date cannot be empty<br>";
            }

            if ($is_set_from_date == true && $is_set_to_date == true) {
                if ($ValidationModel->is_invalid_date_range($FROM_DATE, $TO_DATE)) {
                    $error .= "- Invalid date range<br>";
                }
            }
        }

        return $error;
    }

    public function get_product_filter_result(Request $request)
    {
        $status = $this->get_product_filter_result_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $P_ID = trim($request->input('P_ID'));

            $view = (string)view('Products/Manage_Products_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'P_ID' => $P_ID,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function get_product_filter_result_table(Request $request)
    {
        $status = $this->get_product_filter_result_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $ProductModel = new ProductModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $P_ID = trim($request->input('P_ID'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $ProductModel->get_filtered_product_details($FROM_DATE, $TO_DATE, $P_ID);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('Products/Download/Manage_Products_Table_Download', [
                    'result' => $result,
                ]);
                header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=Filtered Products Download.xls");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false);
                echo $view;
            } else {
                if ($request->ajax()) {
                    return datatables()->of($result)->toJson();
                }
            }
        }
    }

    public function load_in_list_by_product(Request $request)
    {
        $DATA = json_decode(trim($request->input('DATA')));

        $PRODUCT_ID = $DATA->PRODUCT_ID;

        $view = (string)view('Products/Load_Product_In_List', [
            'PRODUCT_ID' => $PRODUCT_ID,
        ]);
        return json_encode(array('result' => $view));
    }

    public function load_out_list_by_product(Request $request)
    {
        $DATA = json_decode(trim($request->input('DATA')));

        $PRODUCT_ID = $DATA->PRODUCT_ID;

        $view = (string)view('Products/Load_Product_Out_List', [
            'PRODUCT_ID' => $PRODUCT_ID,
        ]);
        return json_encode(array('result' => $view));
    }

    public function load_available_stock_by_product(Request $request)
    {
        $StockModel = new StockModel();

        $DATA = json_decode(trim($request->input('DATA')));

        $PRODUCT_ID = $DATA->PRODUCT_ID;
        $WAREHOUSES = $StockModel->get_active_stock_locations();

        $view = (string)view('Products/Load_Product_Available_Stock', [
            'PRODUCT_ID' => $PRODUCT_ID,
            'WAREHOUSES' => $WAREHOUSES,
        ]);
        return json_encode(array('result' => $view));
    }

    public function validate_in_list_by_product_filter_result($request)
    {
        $ValidationModel = new ValidationModel();
        $error = "";

        $FROM_DATE = trim($request->input('FROM_DATE'));
        $TO_DATE = trim($request->input('TO_DATE'));
        $PRODUCT_ID = trim($request->input('PRODUCT_ID'));

        $is_set_from_date = true;
        $is_set_to_date = true;

        if ($ValidationModel->is_invalid_data($FROM_DATE)) {
            $is_set_from_date = false;
            $error .= "- From Date cannot be empty<br>";
        }
        if ($ValidationModel->is_invalid_data($TO_DATE)) {
            $is_set_to_date = false;
            $error .= "- To Date cannot be empty<br>";
        }

        if ($is_set_from_date == true && $is_set_to_date == true) {
            if ($ValidationModel->is_invalid_date_range($FROM_DATE, $TO_DATE)) {
                $error .= "- Invalid date range<br>";
            }
        }

        return $error;
    }

    public function load_in_list_by_product_table(Request $request)
    {
        $status = $this->validate_in_list_by_product_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $PRODUCT_ID = trim($request->input('PRODUCT_ID'));

            $view = (string)view('Products/Load_Product_In_List_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'PRODUCT_ID' => $PRODUCT_ID,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function load_out_list_by_product_table(Request $request)
    {
        $status = $this->validate_in_list_by_product_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $PRODUCT_ID = trim($request->input('PRODUCT_ID'));

            $view = (string)view('Products/Load_Product_Out_List_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'PRODUCT_ID' => $PRODUCT_ID,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function load_available_stock_by_product_table(Request $request)
    {
        $PRODUCT_ID = trim($request->input('PRODUCT_ID'));
        $MW_ID = trim($request->input('MW_ID'));

        $view = (string)view('Products/Load_Product_Available_Stock_Table', [
            'MW_ID' => $MW_ID,
            'PRODUCT_ID' => $PRODUCT_ID,
        ]);
        return json_encode(array('result' => $view));
    }

    public function load_in_list_by_product_table_data(Request $request)
    {
        $status = $this->validate_in_list_by_product_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $ProductModel = new ProductModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $PRODUCT_ID = trim($request->input('PRODUCT_ID'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $ProductModel->get_filtered_in_list_products($FROM_DATE, $TO_DATE, $PRODUCT_ID);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('User/Download/Product_In_List_Download', [
                    'result' => $result,
                ]);
                header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=Filtered Users Download.xls");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false);
                echo $view;
            } else {
                if ($request->ajax()) {
                    return datatables()->of($result)->toJson();
                }
            }
        }
    }

    public function load_out_list_by_product_table_data(Request $request)
    {
        $status = $this->validate_in_list_by_product_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $ProductModel = new ProductModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $PRODUCT_ID = trim($request->input('PRODUCT_ID'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $ProductModel->get_filtered_out_list_products($FROM_DATE, $TO_DATE, $PRODUCT_ID);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('User/Download/Product_In_List_Download', [
                    'result' => $result,
                ]);
                header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=Filtered Users Download.xls");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false);
                echo $view;
            } else {
                if ($request->ajax()) {
                    return datatables()->of($result)->toJson();
                }
            }
        }
    }

    public function load_available_stock_by_product_table_data(Request $request)
    {
        $ProductModel = new ProductModel();
        $MW_ID = trim($request->input('MW_ID'));
        $PRODUCT_ID = trim($request->input('PRODUCT_ID'));
        $DOWNLOAD = trim($request->input('DOWNLOAD'));

        $result = $ProductModel->get_filtered_available_stock_products($MW_ID, $PRODUCT_ID);

        if ($DOWNLOAD == 'YES') {
            $view = (string)view('User/Download/Product_In_List_Download', [
                'result' => $result,
            ]);
            header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=Filtered Users Download.xls");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            echo $view;
        } else {
            if ($request->ajax()) {
                return datatables()->of($result)->toJson();
            }
        }
    }

    public function upload_product_cover(Request $request)
    {
        $CommonModel = new CommonModel();
        $P_ID = trim($request->input('P_ID'));

        DB::beginTransaction();
        try {
            if ($request->hasFile('DOCUMENT')) {
                $data2 = array(
                    'pd_status' => 0,
                    'pd_updated_date' => date('Y-m-d H:i:s'),
                    'pd_updated_by' => session('USER_ID'),
                );
                DB::table('product_documents')
                    ->where('pd_p_id', $P_ID)
                    ->update($data2);

                $file = $request->file('DOCUMENT');
                $filename = time() . '_' . $file->getClientOriginalName();
                $folderPath = 'Products/' . $P_ID;
                $filePath   = $folderPath . '/' . $filename;

                $file->move(public_path($folderPath), $filename);

                $data4 = [
                    'pd_status' => 1,
                    'pd_inserted_date' => now(),
                    'pd_inserted_by' => session('USER_ID'),
                    'pd_p_id' => $P_ID,
                    'pd_file_name' => $filename,
                    'pd_original_name' => $file->getClientOriginalName(),
                    'pd_file_path' => $filePath,
                ];
                DB::table('product_documents')->insert($data4);
            } else {
                return json_encode(array('error' => "Photo not uploaded."));
            }
            $CommonModel->update_work_log(session('USER_ID'), 'Product Cover Photo updated. Product ID:' . $P_ID);

            DB::commit();
            return json_encode(array('success' => 'Product Cover Photo updated.', 'p_id' => $P_ID));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }
}
