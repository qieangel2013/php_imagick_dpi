PHP把非dpi转化为dpi图片
使用方法如下：<br/>
      $filename = $path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'].'/'.$lspatharr[0].'-'.$v['updateid'].'.'.$lspatharr[1];<br/>
      $file = file_get_contents($filename);<br/>
                        //数据块长度为9<br/>
                    $len = pack("N", 9);<br/>
                    //数据块类型标志为pHYs<br/>
                    $sign = pack("A*", "pHYs");<br/>
                    //X方向和Y方向的分辨率均为300DPI（1像素/英寸=39.37像素/米），单位为米（0为未知，1为米）<br/>
                    $data = pack("NNC", 72 * 39.37, 72 * 39.37, 0x01);<br/>
                    //CRC检验码由数据块符号和数据域计算得到<br/>
                    $checksum = pack("N", crc32($sign . $data));<br/>
                    $phys = $len . $sign . $data . $checksum;<br/>
                    $pos = strpos($file, "pHYs");<br/>
                    if ($pos > 0) {<br/>
                    //修改pHYs数据块<br/>
                    $file = substr_replace($file, $phys, $pos - 4, 21);<br/>
                    } else {<br/>
                    //IHDR结束位置（PNG头固定长度为8，IHDR固定长度为25）<br/>
                    $pos = 33;<br/>
                    //将pHYs数据块插入到IHDR之后<br/>
                    $file = substr_replace($file, $phys, $pos, 0);<br/>
                    }<br/>
                         $filedata = fopen($path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'].'/'.$lspatharr[0].'-'.$v['updateid'].'.'.$lspatharr[1],"w");//打开文件准备写入 <br/>
                         fwrite($filedata,$file);//写入 <br/>
                         fclose($filedata);//关闭<br/>
                         echo "图片压缩成功！";<br/>
                    }<br/>
                    ===
   imagick把灰度图片转化为rgb图片
   $im_get=new \Imagick($path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'].'/'.$lspatharr[0].'-'.$v['updateid'].'.'.$lspatharr[1]);<br/>
                     $im_sta=$im_get->getImageColorspace();<br/>
                     if(2==$im_sta){<br/>
                         list($bg_width,$bg_height)=getimagesize($path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'].'/'.$lspatharr[0].'-'.$v['updateid'].'.'.$lspatharr[1]);<br/>
                         $bg_width -=2;<br/>
                         $bg_height -=2;<br/>
                         $imm= new \Imagick();<br/>
                         $imm->newImage(2,2,new \ImagickPixel('#FFFFFE'));<br/>
                         $im_get->setImageColorSpace(1);<br/>
                         $dww = new \ImagickDraw();<br/>
                         $dww->setGravity(5);<br/>
                         $dww->setFillOpacity(0.1);<br/>
                         $dww->composite($imm->getImageCompose(), -$bg_width/2, $bg_height/2, 0, 0, $imm);<br/>
                         $im_get->drawImage($dww);<br/>
                         $im_get->writeImage($path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'].'/'.$lspatharr[0].'-'.$v['updateid'].'.'.$lspatharr[1]);<br/>
                     }<br/>
            操作imagick之前，得先安装imagick扩展
            有什么问题可以及时联系我 qieangel@hotmail.com
