<?php

class itemcode_model extends Model {

    function itemcode_model() {
        parent::Model();
    }

    function get_itemcodes($limit = 0, $offset = 0) {
        if ($offset == 0) {
            $newoffset = 0;
        } else {
            $newoffset = $offset;
        }
        $ti = $this->db->dbprefix('item');
        $ta = $this->db->dbprefix('award');
        $tai = $this->db->dbprefix('awarditem');

        $pa = $this->session->userdata('purchasingadmin');

        $where = " WHERE 1=1 ";
        if(@$_POST['searchitemname'])
            $where .= " AND i.itemname LIKE '%{$_POST['searchitemname']}%' OR i.itemcode LIKE '%{$_POST['searchitemname']}%'";
        if(@$_POST['searchcategory'])
            $where .= " AND i.category = '{$_POST['searchcategory']}'";
        //$where .= " AND ai.purchasingadmin='$pa'";
        $sql = "SELECT i.*, MAX(a.awardedon) awardedon, sum(ai.totalprice) totalpurchase
                FROM
                $ti i
                LEFT JOIN $tai ai ON i.id=ai.itemid
                LEFT JOIN $ta a ON ai.award=a.id AND ai.purchasingadmin='$pa'
                $where
                GROUP BY i.id
                ORDER BY awardedon DESC";
        //echo $sql;
        $query = $this->db->query($sql);
        if ($query->result()) {
            $result = $query->result();
            $ret = array();
            foreach ($result as $item)
            {
                $item->poitems = $this->getpoitems($item->id);
                $item->minprices = $this->getminimumprices($item->id);
                $item->tierprices = $this->gettierprices($item->id);

                //if($item->poitems && @$item->poitems[0])
                    //$item->awardedon = $item->poitems[0]->awardedon;

                $item->totalpoprice = 0;
                if ($item->poitems)
                    foreach ($item->poitems as $po) {
                        $item->totalpoprice += $po->totalprice;
                    }

                $sql2 = "SELECT (SUM(od.quantity * od.price) + (SUM(od.quantity * od.price) * o.taxpercent / 100))
		    	 totalprice2 FROM ".$this->db->dbprefix('order')." o, ".$this->db->dbprefix('orderdetails')." od
                WHERE od.orderid=o.id AND od.itemid = ".$item->id." AND o.purchasingadmin='$pa'";
                $query2 = $this->db->query($sql2);
                 if ($query2->result()) {
            		$result2 = $query2->result();
                	if(isset($result2[0]->totalprice2))
                	$item->totalpoprice += $result2[0]->totalprice2;
                 }

                //$item->minprices = $this->getminimumprices($item->itemcode);
                $ret[] = $item;
            }
            //print_r($ret);die;
            return $ret;
        } else {
            return null;
        }
    }

    function get_itemcodescatcode($subcatid)
    {
        if ($offset == 0) {
            $newoffset = 0;
        } else {
            $newoffset = $offset;
        }

        $sql = "SELECT *
		FROM
		" . $this->db->dbprefix('item') . " where subcategory=" . $subcatid . " ORDER BY itemname ASC";

        $query = $this->db->query($sql);
        if ($query->result()) {
            $result = $query->result();
            $ret = array();
            foreach ($result as $item) {
                $item->poitems = $this->getpoitems($item->id);

                $item->minprices = $this->getminimumprices($item->id);

                $item->totalpoprice = 0;
                if ($item->poitems)
                    foreach ($item->poitems as $po) {
                        $item->totalpoprice += $po->totalprice;
                    }
                $item->minprices = $this->getminimumprices($item->itemcode);
                $ret[] = $item;
            }
            //print_r($ret);die;
            return $ret;
        } else {
            return null;
        }
    }

    // counting total itemcodes
    function total_itemcode() {
        $query = $this->db->count_all_results('item');
        return $query;
    }

    function getcategories()
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix('category') . "
        		WHERE id NOT IN (SELECT distinct(parent_id) FROM " . $this->db->dbprefix('category') . ")";
        $leaves = $this->db->query($sql)->result();
        $ret = array();

        foreach($leaves as $leaf)
        {
            $parent = $leaf->parent_id;
            while($parent)
            {
                $sql = "SELECT * FROM " . $this->db->dbprefix('category') . " WHERE id='$parent'  ";
                $pcat = $this->db->query($sql)->row();
                if($pcat)
                {
                    $parent = $pcat->parent_id;
                    $leaf->catname = $pcat->catname . ' > ' . $leaf->catname;
                }
                else
                {
                    break 1;
                }
            }
            
            $sql1 = "SELECT * FROM " . $this->db->dbprefix('item') . " WHERE category='$leaf->id'  ";
            $item = $this->db->query($sql1)->result(); 
            $count=number_format(count($item));
            $leaf->catname .="(".$count.")";
            
            $ret[] = $leaf;
        }
        //echo '<pre>'; print_r($ret);//die;
        $this->aasort($ret, 'catname');
        //echo '<pre>'; print_r($ret);die;

        return $ret;

    }
	function aasort (&$array, $key)
	{
	    $sorter=array();
	    $ret=array();
	    reset($array);
	    foreach ($array as $ii => $va)
	    {
	        $sorter[$ii]=$va->$key;
	    }
	    $sortflag = 14;//SORT_NATURAL ^ SORT_FLAG_CASE;

	    asort($sorter, $sortflag );
	    foreach ($sorter as $ii => $va)
	    {
	        $ret[$ii]=$array[$ii];
	    }
	    $array=$ret;
	}

    function getpoitems($itemid)
    {
        $projectwhere = '';
        $mp = $this->session->userdata('managedprojectdetails');
        if($mp)
            $projectwhere = " AND q.pid='".$mp->id."'";
        $sql = "SELECT ai.*, c.title companyname, q.ponum, a.awardedon, a.quote
			   	FROM
				" . $this->db->dbprefix('awarditem') . " ai, " . $this->db->dbprefix('award') . " a,
				" . $this->db->dbprefix('quote') . " q, " . $this->db->dbprefix('company') . " c
				WHERE
				ai.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "' AND
				ai.award=a.id AND a.quote=q.id AND ai.company=c.id AND ai.itemid='$itemid'
				$projectwhere
				ORDER BY a.awardedon DESC
				";
        //echo $sql;
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            $result = $query->result();
            $ret = array();
            foreach ($result as $item) {
                $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
                $this->db->where('company', $item->company);
                if ($this->db->get('network')->result()) {
                    $ret[] = $item;
                }
            }
            
            return $ret;
        }
        return NULL;
    }

    function getpoitems2($itemid)
    {
    	$projectwhere = '';
    	$mp = $this->session->userdata('managedprojectdetails');
    	if($mp)
    	$projectwhere = " AND q.pid='".$mp->id."'";
    	$sql = "SELECT sum(ai.totalprice) as totalprice, ai.company, ai.ea, ai.daterequested as daterequested, c.title companyname, GROUP_CONCAT(q.ponum,' : $',ai.totalprice) as ponum, a.awardedon, a.quote
			   	FROM
				" . $this->db->dbprefix('awarditem') . " ai, " . $this->db->dbprefix('award') . " a,
				" . $this->db->dbprefix('quote') . " q, " . $this->db->dbprefix('company') . " c
				WHERE
				ai.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "' AND
				ai.award=a.id AND a.quote=q.id AND ai.company=c.id AND ai.itemid='$itemid'
				$projectwhere
        		group by ai.daterequested
				ORDER BY ai.daterequested DESC

				";
    	//echo $sql;
    	$query = $this->db->query($sql);
    	if ($query->num_rows > 0) {
    		$result = $query->result();
    		$ret = array();
    		foreach ($result as $item) {
    			$this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
    			$this->db->where('company', $item->company);
    			if ($this->db->get('network')->result()) {
    				$ret[] = $item;
    			}
    		}
    		return $ret;
    	}
    	return NULL;
    }

    function getminimumprices($itemid)
    {
        $sql = "SELECT c.title companyname, m.*
			   	FROM
				" . $this->db->dbprefix('minprice') . " m,
				" . $this->db->dbprefix('company') . " c
				WHERE
				m.company=c.id AND m.itemid='$itemid'
				AND m.purchasingadmin='".$this->session->userdata('purchasingadmin')."'
				";
        //echo $sql; die;
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            $result = $query->result();
            $ret = array();
            foreach ($result as $item) {
                $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
                $this->db->where('company', $item->company);
                if ($this->db->get('network')->result()) {
                    $ret[] = $item;
                }
            }
            return $ret;
        }
        return NULL;
    }

    function gettierprices($itemid)
    {
        $pa = $this->session->userdata('purchasingadmin');
        $sql = "select c.id companyid, c.title companyname, ci.ea listprice
				FROM " . $this->db->dbprefix('company') . " c,
				" . $this->db->dbprefix('companyitem') . " ci,
				" . $this->db->dbprefix('network') . " n
				WHERE c.id=ci.company AND c.id=n.company AND
				n.purchasingadmin='" . $pa . "'
				AND ci.itemid='" . $itemid . "' AND ci.ea != 0";
        //echo $sql;//die;
        $recs = $this->db->query($sql)->result();
        $ret = array();
        foreach ($recs as $rec)
        {
            $rec->price = $rec->listprice;
            $sql = "select tier from " . $this->db->dbprefix('purchasingtier') . "
				    where purchasingadmin='$pa' AND company='" . $rec->companyid . "'";

            $tier = $this->db->query($sql)->row();
            if ($tier)
            {
                $tier = $tier->tier;
                $this->db->where('company', $rec->companyid);
                $pt = $this->db->get('tierpricing')->row();
                if ($pt)
                {
                    $deviation = $pt->$tier;
                    $price = $rec->listprice + ($rec->listprice * $deviation / 100);
                    $price = number_format($price, 2);
                    $rec->price = $price;
                }
            }

            $ret[] = $rec;
        }
        return $ret;
    }

        function gettierpriceswithqtydiscount($itemid,$quantity)
    {
        $pa = $this->session->userdata('purchasingadmin');
        $sql = "select c.id companyid, c.title companyname, ci.ea listprice
				FROM " . $this->db->dbprefix('company') . " c,
				" . $this->db->dbprefix('companyitem') . " ci,
				" . $this->db->dbprefix('network') . " n
				WHERE c.id=ci.company AND c.id=n.company AND
				n.purchasingadmin='" . $pa . "'
				AND ci.itemid='" . $itemid . "' AND ci.ea != 0";
        //echo $sql;//die;
        $recs = $this->db->query($sql)->result();
        $ret = array();
        foreach ($recs as $rec)
        {
        	$discountedquantityprice = $this->getnewprice($itemid,$rec->companyid, $quantity);
        	if($discountedquantityprice>0){
        		$rec->price = $discountedquantityprice;
        		$rec->listprice = $discountedquantityprice;
        	}else
           	    $rec->price = $rec->listprice;

            $sql = "select tier from " . $this->db->dbprefix('purchasingtier') . "
				    where purchasingadmin='$pa' AND company='" . $rec->companyid . "'";

            $sqltier = "select tierprice from " . $this->db->dbprefix('companyitem') . "
				    where itemid='$itemid' AND company='" . $rec->companyid . "' AND type = 'Supplier'";

            $istierprice = $this->db->query($sqltier)->row();
            if($istierprice){
            	$istier = $istierprice->tierprice;
            }else
            	$istier = 0;

            $tier = $this->db->query($sql)->row();
            if ($tier && $istier)
            {
                $tier = $tier->tier;
                $this->db->where('company', $rec->companyid);
                $pt = $this->db->get('tierpricing')->row();
                if ($pt)
                {
                    $deviation = $pt->$tier;
                    $price = $rec->listprice + ($rec->listprice * $deviation / 100);
                    $price = number_format($price, 2);
                    $rec->price = $price;
                }
            }

            $ret[] = $rec;
        }
        return $ret;
    }

    function getnewprice($itemid,$companyid, $quantity){

    	$sql1 = "SELECT * FROM ".$this->db->dbprefix('qtydiscount')." WHERE company = '{$companyid}' and itemid = '{$itemid}' and qty <= '{$quantity}' order by qty desc limit 1";
    	$result1 = $this->db->query($sql1)->row();
    	if($result1){
			return $result1->price;
    	}else{
    		return 0;
    	}die;

    }

    function SaveItemcode() {   	
    	$name=implode(",",$_FILES['UploadFile']['name']);
    	$filename=implode(",",$_POST['filename']);   
    	$tag = $this->input->post('tags');
    	$tag = str_replace("\n",",",$tag);
     
    	$primarycategory = $_POST['category'][0];
	        $options = array(
            'itemcode' => $this->input->post('itemcode'),
            'itemname' => $this->input->post('itemname'),
            'description' => $this->input->post('description'),
            'details' => $this->input->post('details'),
            'unit' => $this->input->post('unit'),
            'ea' => $this->input->post('ea'),
            'notes' => $this->input->post('notes'),
            'keyword' => $this->input->post('keyword'),
            'category' => $primarycategory, //$this->input->post('catid'),
            'item_img' => $_FILES["userfile"]["name"],
            'external_url' => $this->input->post('external_url'),
        	'length' => $this->input->post('length'),
        	'width' => $this->input->post('width'),
        	'height' => $this->input->post('height'),
        	'weight' => $this->input->post('weight'),
            'featuredsupplier' => $this->input->post('featuredsupplier'),
            'instore' => 1,//$this->input->post('instore'),
            'wiki' => $this->input->post('wiki'),
            'url' => $this->input->post('url'),
            'listinfo' => $this->input->post('listinfo'),
	        'tags' => $tag,
	        'files' => $name,
	        'filename' => $filename,
	        'searchquery' => $this->input->post('searchquery'),
	        'increment' => $this->input->post('increment')
        );

        $this->db->insert('item', $options);
        $id = $this->db->insert_id();
		
        foreach ($_POST['category'] as $category){
        	$options2 = array();
        	$options2['itemid'] = $id;
        	$options2['categoryid'] = $category;
        	$this->db->insert('item_category', $options2);
        }
        
        $categories = $this->input->post('catid');
        if ($categories !== false) {
            if ($id) {
                $cats = $this->get_product_categories($id);
                $ids = array();
                foreach ($cats as $c) {
                    $ids[] = $c->id;
                }
                foreach ($ids as $c) {
                    if (!in_array($c, $categories)) {
                        $this->db->delete($this->db->dbprefix('category_products'), array('product_id' => $id, 'category_id' => $c));
                    }
                }

                //add products to new categories
                foreach ($categories as $c) {
                    if (!in_array($c, $ids)) {
                        $this->db->insert($this->db->dbprefix('category_products'), array('product_id' => $id, 'category_id' => $c));
                    }
                }
            }
        }
        return $id;
    }

    function get_product_categories($id) {
        return $this->db->where('product_id', $id)->join($this->db->dbprefix('category'), 'category_id = category.id')->get($this->db->dbprefix('category_products'))->result();
    }



// updating cost code
    function updateItemcode($id) {
    	
    	$name=implode(",",$_FILES['UploadFile']['name']);
    	$filename=implode(",",$_POST['filename']);  
    	
    	$zoom;
		if(@$this->input->post('zoom') == "on")
    		$zoom = 1;
    	else
    		$zoom = 0;
		$tag = $this->input->post('tags');
    	$tag = str_replace("\n",",",$tag);
    	
    	$primarycategory = $_POST['category'][0];
        $options = array(
            'itemcode' => $this->input->post('itemcode'),
            'itemname' => $this->input->post('itemname'),
            'description' => $this->input->post('description'),
            'details' => $this->input->post('details'),
            'unit' => $this->input->post('unit'),
            'ea' => $this->input->post('ea'),
            'notes' => $this->input->post('notes'),
            'keyword' => $this->input->post('keyword'),
            'category' => $primarycategory,
            'item_img' => $_FILES["userfile"]["name"],
            'external_url' => $this->input->post('external_url'),
        		'length' => $this->input->post('length'),
        		'width' => $this->input->post('width'),
        		'height' => $this->input->post('height'),
        		'weight' => $this->input->post('weight'),
            'featuredsupplier' => $this->input->post('featuredsupplier'),
            'instore' => 1,//$this->input->post('instore'),
            'zoom' => $zoom,
            'wiki' => $this->input->post('wiki'),
            'url' => $this->input->post('url'),
            'listinfo' => $this->input->post('listinfo'),
            'item_img_alt_text' => $this->input->post('item_img_alt_text'),
        	'tags' => $tag,
        	'files' => $name,
	        'filename' => $filename,
	        'searchquery' => $this->input->post('searchquery'),
	        'increment' => $this->input->post('increment')
        );
        
    	if($_FILES["userfile"]["name"]=="")
    	{
    		$updatedata=$this->db->get_where('item',array('id'=>$this->input->post('id')))->row();
    		$options['item_img'] = $updatedata->item_img;
    	}
    	else 
    	{
    		$options['item_img'] = $_FILES["userfile"]["name"];
    	}
        $oldcoderow = $this->get_itemcodes_by_id($this->input->post('id'));
        $oldcode = $oldcoderow->itemcode;
        $newcode = $this->input->post('itemcode');

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('item', $options);

        $this->db->where('itemid', $this->input->post('id'));        
        $this->db->delete('item_category');

        foreach ($_POST['category'] as $category){
        	$options2 = array();
        	$options2['itemid'] = $this->input->post('id');
        	$options2['categoryid'] = $category;
        	$this->db->insert('item_category', $options2);
        }
        
        
        if(!$options['instore'])
        {
            $this->db->where('itemid', $this->input->post('id'));
            $this->db->where('type','Supplier');
            $this->db->delete('companyitem');
        }

        $update = array('itemcode' => $newcode);

        $this->db->where('itemcode', $oldcode);
        $this->db->update('awarditem', $update);

        $this->db->where('itemcode', $oldcode);
        $this->db->update('biditem', $update);

        $this->db->where('itemcode', $oldcode);
        $this->db->update('quoteitem', $update);

        $categories = $this->input->post('catid');
        if ($categories !== false) {
            if ($id = $this->input->post('id')) {
                $cats = $this->get_product_categories($id);
                $ids = array();
                foreach ($cats as $c) {
                    $ids[] = $c->id;
                }
                foreach ($ids as $c) {
                    if (!in_array($c, $categories)) {
                        $this->db->delete($this->db->dbprefix('category_products'), array('product_id' => $id, 'category_id' => $c));
                    }
                }

                //add products to new categories
                foreach ($categories as $c) {
                    if (!in_array($c, $ids)) {
                        $this->db->insert($this->db->dbprefix('category_products'), array('product_id' => $id, 'category_id' => $c));
                    }
                }
            }
        }
    }

    // removing cost code
    function remove_itemcode($id) {
        $item = $this->get_itemcodes_by_id($id);
        $this->db->where('id', $id);
        $this->db->delete('item');
        /*
          $this->db->where('itemcode', $item->itemcode);
          $this->db->delete('quoteitem');

          $this->db->where('itemcode', $item->itemcode);
          $this->db->delete('biditem');
         */
    }

    // retrieve cost code by their id
    //function get_itemcodes_by_id($id,$quantity) {
    function get_itemcodes_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('item');
        if ($query->num_rows > 0) {
            $ret = $query->row();
            $ret->minprices = $this->getminimumprices($id);
            $ret->poitems = $this->getpoitems($id);
            $ret->tierprices = $this->gettierprices($id);


            // LAST QUOTED DATE
            $lastsql = "SELECT a.awardedon lastdate
				FROM " . $this->db->dbprefix('awarditem') . " ai, " . $this->db->dbprefix('award') . " a
				WHERE a.id=ai.award AND ai.itemcode='" . $ret->itemcode . "' AND ai.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'
				ORDER BY b.submitdate DESC LIMIT 0,1
				";

            $lastsql = "SELECT b.submitdate lastdate
				FROM " . $this->db->dbprefix('biditem') . " bi, " . $this->db->dbprefix('bid') . " b
				WHERE b.id=bi.bid AND bi.itemcode='" . $ret->itemcode . "' AND bi.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'
				ORDER BY b.submitdate DESC LIMIT 0,1
				";

            $lastquery = $this->db->query($lastsql);
            if ($lastquery->num_rows > 0)
                $ret->lastquoted = $lastquery->row()->lastdate;
            else
                $ret->lastquoted = '';


            // TARGET PRICE
            $sqlmin = "SELECT MIN(ea) minprice FROM " . $this->db->dbprefix('biditem') . "
						WHERE itemcode='" . $ret->itemcode . "' AND purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'";
            $minprice = $this->db->query($sqlmin)->row()->minprice;
            $sqlconfig = "SELECT * FROM " . $this->db->dbprefix('settings') . " WHERE id='1'";
            $queryconfig = $this->db->query($sqlconfig);
            $pricepercent = $queryconfig->row()->pricepercent;

            $ret->targetprice = $minprice - ($minprice * $pricepercent / 100);


            //$ret->product_categories = $this->get_product_categories($id);

            return $ret;
        }
        return NULL;
    }



     function get_itemcodes_by_id2($id, $quantity) {
        $this->db->where('id', $id);
        $query = $this->db->get('item');
        if ($query->num_rows > 0) {
            $ret = $query->row();
            $ret->minprices = $this->getminimumprices($id);
            $ret->poitems = $this->getpoitems($id);
            $ret->tierprices = $this->gettierpriceswithqtydiscount($id, $quantity); // $quantity var not fount


            // LAST QUOTED DATE
            $lastsql = "SELECT a.awardedon lastdate
				FROM " . $this->db->dbprefix('awarditem') . " ai, " . $this->db->dbprefix('award') . " a
				WHERE a.id=ai.award AND ai.itemcode='" . $ret->itemcode . "' AND ai.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'
				ORDER BY b.submitdate DESC LIMIT 0,1
				";

            $lastsql = "SELECT b.submitdate lastdate
				FROM " . $this->db->dbprefix('biditem') . " bi, " . $this->db->dbprefix('bid') . " b
				WHERE b.id=bi.bid AND bi.itemcode='" . $ret->itemcode . "' AND bi.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'
				ORDER BY b.submitdate DESC LIMIT 0,1
				";

            $lastquery = $this->db->query($lastsql);
            if ($lastquery->num_rows > 0)
                $ret->lastquoted = $lastquery->row()->lastdate;
            else
                $ret->lastquoted = '';


            // TARGET PRICE
            $sqlmin = "SELECT MIN(ea) minprice FROM " . $this->db->dbprefix('biditem') . "
						WHERE itemcode='" . $ret->itemcode . "' AND purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'";
            $minprice = $this->db->query($sqlmin)->row()->minprice;
            $sqlconfig = "SELECT * FROM " . $this->db->dbprefix('settings') . " WHERE id='1'";
            $queryconfig = $this->db->query($sqlconfig);
            $pricepercent = $queryconfig->row()->pricepercent;

            $ret->targetprice = $minprice - ($minprice * $pricepercent / 100);


            //$ret->product_categories = $this->get_product_categories($id);

            return $ret;
        }
        return NULL;
    }

    // retrieve cost code by their id
    function get_itemcodes_by_code($code) {
        $this->db->where('itemcode', $code);
        $query = $this->db->get('item');
        if ($query->num_rows > 0) {
            $ret = $query->row();
            $ret->minprices = $this->getminimumprices($ret->itemcode);
            $ret->poitems = $this->getpoitems($ret->itemcode);
            $ret->tierprices = $this->gettierprices($ret->id);

            // LAST QUOTED DATE
            $lastsql = "SELECT b.submitdate lastdate
				FROM " . $this->db->dbprefix('biditem') . " bi, " . $this->db->dbprefix('bid') . " b
				WHERE b.id=bi.bid AND bi.itemcode='" . $ret->itemcode . "' bi.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'
				ORDER BY b.submitdate DESC LIMIT 0,1
				";

            $lastquery = $this->db->query($lastsql);
            if ($lastquery->num_rows > 0)
                $ret->lastquoted = $lastquery->row()->lastdate;
            else
                $ret->lastquoted = '';

            return $ret;
        }
        return NULL;
    }

    function getlowestquoteprice($itemid, $potype = 'Bid') {
        //if($potype == 'Bid')
        $sql = "SELECT MIN(ea) minprice FROM " . $this->db->dbprefix('biditem') . " WHERE itemid='$itemid' AND purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'";

        //if($potype == 'Direct')
        //$sql = "SELECT MIN(ea) minprice FROM ".$this->db->dbprefix('biditem')." WHERE itemcode='$itemid'";
        $row = $this->db->query($sql)->row();
        return $row->minprice;
    }

    function getdaysmeanprice($itemid, $potype = 'Bid') {
        $pricedays = $this->db->get('settings')->row()->pricedays;
        $sql = "SELECT AVG(ea) avgprice FROM " . $this->db->dbprefix('awarditem') . " ai, " . $this->db->dbprefix('award') . " a
			  WHERE ai.itemid='$itemid' AND ai.award=a.id AND DATEDIFF(NOW(),a.awardedon) <=$pricedays
              AND ai.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'";

        $sql = "SELECT AVG(ea) avgprice FROM " . $this->db->dbprefix('biditem') . " bi,
        		" . $this->db->dbprefix('bid') . " b
			  WHERE bi.itemid='$itemid' AND bi.bid=b.id AND DATEDIFF(NOW(),b.submitdate) <=$pricedays
              AND bi.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'";
        //echo $sql.'<br>';
        $row = $this->db->query($sql)->row();
        return $row->avgprice;
    }

    function checkDuplicateCode($code, $edit_id = 0) {
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id, 'itemcode' => $code));
        } else {
            $this->db->where('itemcode', $code);
        }
        $query = $this->db->get('item');
        $result = $query->result();

        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function checkDuplicateUrl($url, $edit_id = 0) {
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id, 'url' => $url));
        } else {
            $this->db->where('url', $url);
        }
        $query = $this->db->get('item');
        $result = $query->result();

        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function checkDuplicateArticleUrl($url, $edit_id = 0) {
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id, 'url' => $url));
        } else {
            $this->db->where('url', $url);
        }
        $query = $this->db->get('itemarticle');
        $result = $query->result();

        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Start By Dhruvisha On 11th Jan 2014
    function SaveCategory() {
        //var_dump($this->input->post('catname')); exit;
        $options = array(
            'catname' => $this->input->post('catname')
        );
        $this->db->insert('category', $options);
        return $this->db->insert_id();
    }

    function checkDuplicateCat($catname, $edit_id = 0) {
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id, 'catname' => $catname));
        } else {
            $this->db->where('catname', $catname);
        }
        $query = $this->db->get('category');
        $result = $query->result();

        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function getCategoryList() {
        $sql = "SELECT *
		FROM
		" . $this->db->dbprefix('category') . " ORDER BY catname ASC";

        $query = $this->db->query($sql);
        if ($query->result()) {
            $result = $query->result();
            return $result;
        } else {
            return null;
        }
    }

    function getSubCategoryList($catid = 0) {
        $sql = "SELECT *
		FROM
		" . $this->db->dbprefix('subcategory') . "";
        if ($catid > 0) {
            $sql.=" where category=" . $catid;
        }
        $sql.=" ORDER BY subcategory ASC";

        $query = $this->db->query($sql);
        if ($query->result()) {
            $result = $query->result();
            return $result;
        } else {
            return null;
        }
    }

    function checkDuplicateSubCat($catname, $subcatname, $edit_id = 0) {
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id, 'subcategory' => $subcatname, 'category' => $catname));
        } else {
            $this->db->where(array('category' => $catname, 'subcategory' => $subcatname));
        }
        $query = $this->db->get('subcategory');
        $result = $query->result();

        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function SaveSubCategory() {

        $options = array(
            'category' => $this->input->post('catid'),
            'subcategory' => $this->input->post('subcat')
        );
        $this->db->insert('subcategory', $options);
        return $this->db->insert_id();
    }

//End By Dhruvisha
}

?>