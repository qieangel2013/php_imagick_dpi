PHP把非dpi转化为dpi图片
===================================
### 使用方法如下：
        $filename = $path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'].'/'.$lspatharr[0].'-'.$v['updateid'].'.'.$lspatharr[1];
      $file = file_get_contents($filename);
                        //数据块长度为9
                    $len = pack("N", 9);
                    //数据块类型标志为pHYs
                    $sign = pack("A*", "pHYs");
                    //X方向和Y方向的分辨率均为300DPI（1像素/英寸=39.37像素/米），单位为米（0为未知，1为米）
                    $data = pack("NNC", 72 * 39.37, 72 * 39.37, 0x01);
                    //CRC检验码由数据块符号和数据域计算得到
                    $checksum = pack("N", crc32($sign . $data));
                    $phys = $len . $sign . $data . $checksum;
                    $pos = strpos($file, "pHYs");
                    if ($pos > 0) {
                    //修改pHYs数据块
                    $file = substr_replace($file, $phys, $pos - 4, 21);
                    } else {
                    //IHDR结束位置（PNG头固定长度为8，IHDR固定长度为25）
                    $pos = 33;
                    //将pHYs数据块插入到IHDR之后
                    $file = substr_replace($file, $phys, $pos, 0);
                    }
                         $filedata = fopen($path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'].'/'.$lspatharr[0].'-'.$v['updateid'].'.'.$lspatharr[1],"w");//打开文件准备写入 
                         fwrite($filedata,$file);//写入 
                         fclose($filedata);//关闭
                         echo "图片压缩成功！";
                    }
### imagick把灰度图片转化为rgb图片
   $im_get=new \Imagick($path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'].'/'.$lspatharr[0].'-'.$v['updateid'].'.'.$lspatharr[1]);
                     $im_sta=$im_get->getImageColorspace();
                     if(2==$im_sta){
                         list($bg_width,$bg_height)=getimagesize($path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'].'/'.$lspatharr[0].'-'.$v['updateid'].'.'.$lspatharr[1]);
                         $bg_width -=2;
                         $bg_height -=2;
                         $imm= new \Imagick();
                         $imm->newImage(2,2,new \ImagickPixel('#FFFFFE'));
                         $im_get->setImageColorSpace(1);
                         $dww = new \ImagickDraw();
                         $dww->setGravity(5);
                         $dww->setFillOpacity(0.1);
                         $dww->composite($imm->getImageCompose(), -$bg_width/2, $bg_height/2, 0, 0, $imm);
                         $im_get->drawImage($dww);
                         $im_get->writeImage($path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'].'/'.$lspatharr[0].'-'.$v['updateid'].'.'.$lspatharr[1]);
                     }
### 操作imagick之前，得先安装imagick扩展
### 有什么问题可以及时联系我 qieangel@hotmail.com
