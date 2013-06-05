<?php 
Class OC_mailing_list {
	
	public static function addMemberFromRequest($request) {
		
		//if member exists with email address, update lists.
		$query = OC_DB::prepare('SELECT member_id FROM *PREFIX*mailing_list WHERE member_email = ?');
		$result = $query->execute( array( $request['member_email'] ) );
		$data = $result->fetchRow();
		
		if ( $data ) {

			$updateQuery = OCP\DB::prepare('UPDATE *PREFIX*mailing_list SET member_mailing_lists = ?, member_name = ? WHERE member_email = ?');
			$updateQuery->execute( array ( $request['member_lists'], $request['member_name'], $request['member_email'] ) );

			return $data['member_id'] ." Updated.";

		} else {
			//else insert new member
			$query = OCP\DB::prepare('INSERT INTO *PREFIX*mailing_list (member_name, member_email, member_mailing_lists, member_since) VALUES (?, ?, ?, ?)');
			$query->execute( Array( $request['member_name'], $request['member_email'], $request['member_lists'], date("Y-m-d")));
	
			$member_id = OC_DB::insertid();
			return $member_id ." Added.";
		}
	}


	public static function removeMemberFromRequest($member_id) {
		
		$query = OC_DB::prepare('DELETE from *PREFIX*mailing_list WHERE member_id = ? ');
		$query->execute( array($member_id) );
		
		return "good bye $member_id";
	}


	public static function updateMailingListName($data) {
		
		foreach($data as $list) {

			$updateQuery = OCP\DB::prepare('UPDATE *PREFIX*mailing_lists SET mailing_list_name = ? WHERE mailing_list_id = ?');
			$updateQuery->execute ( array ( $list['mailing_list_name'], $list['mailing_list_id'] ) );
		}
		return $data;
	}

	
	public static function removeMailingList($mailing_list_remove_id) {
		
		//remove list from all members
		$updateQuery = OCP\DB::prepare("UPDATE *PREFIX*mailing_list SET member_mailing_lists = replace(member_mailing_lists, ? , '')");
		$updateQuery->execute ( array ($mailing_list_remove_id.',') );
		
		//remove list from member_lists table
		$deleteQuery = OCP\DB::prepare("DELETE FROM *PREFIX*mailing_lists WHERE mailing_list_id = ?");
		$deleteQuery->execute ( array ($mailing_list_remove_id) );
		return $mailing_list_remove_id;
	}
	
	
	public static function addMailingList($mailing_list_name) {

		$addQuery = OCP\DB::prepare("INSERT INTO *PREFIX*mailing_lists (mailing_list_name) VALUES (?) ");
		$addQuery->execute ( array ($mailing_list_name) );

	}

	public static function toggleMemberList($request) {
		$query = OCP\DB::prepare('SELECT member_mailing_lists FROM *PREFIX*mailing_list WHERE member_id = ?');
		$result = $query->execute( array($request['member_id']) );
		$data = $result->fetchRow();
		if ($data['member_mailing_lists'] === '') {
			$member_lists = $request['checked'] . ',';
			$update = OCP\DB::prepare('UPDATE *PREFIX*mailing_list SET member_mailing_lists = ? WHERE member_id = ?');
			$update->execute( array($member_lists, $request['member_id']) );
			return($member_lists);
		} else {
			$member_lists = explode(',',$data['member_mailing_lists'], -1);
			if ( in_array("$request[checked]",$member_lists) ) {
				$member_lists = array_diff($member_lists, array($request['checked']));
			} else {
				$member_lists[] = $request['checked'];
			}
			$member_lists = implode(',', $member_lists);
			$member_lists = $member_lists . ',';
			$update = OCP\DB::prepare('UPDATE *PREFIX*mailing_list SET member_mailing_lists = ? WHERE member_id = ?');
			$update->execute( array($member_lists, $request['member_id']) );
			return("$member_lists");
		}
	}

}
class VCard {
    var $_map;
    function parse(&$lines) {
        $this->_map = null;
        $property = new VCardProperty();
        while ($property->parse($lines)) {
            if (is_null($this->_map)) {
                if ($property->name == 'BEGIN') {
                    $this->_map = array();
                }
            } else {
                if ($property->name == 'END') {
                    break;
                } else {
                    $this->_map[$property->name][] = $property;
                }
            }
            $property = new VCardProperty();
        }
        return $this->_map != null;
    }

    function getProperty($name) {
        return $this->_map[$name][0];
    }

    function getProperties($name) {
        return $this->_map[$name];
    }

}

class VCardProperty {
    var $name;          // string
    var $params;        // params[PARAM_NAME] => value[,value...]
    var $value;         // string

    function parse(&$lines) {
        while (list(, $line) = each($lines)) {
            $line = rtrim($line);
            $tmp = split_quoted_string(":", $line, 2);
            if (count($tmp) == 2) {
                $this->value = $tmp[1];
                $tmp = strtoupper($tmp[0]);
                $tmp = split_quoted_string(";", $tmp);
                $this->name = $tmp[0];
                $this->params = array();
                for ($i = 1; $i < count($tmp); $i++) {
                    $this->_parseParam($tmp[$i]);
                }
                if ($this->params['ENCODING'][0] == 'QUOTED-PRINTABLE') {
                    $this->_decodeQuotedPrintable($lines);
                }
                if ($this->params['CHARSET'][0] == 'UTF-8') {
                    $this->value = utf8_decode($this->value);
                }
                return true;
            }
        }
        return false;
    }

    function getComponents($delim = ";") {
        $value = $this->value;
        // Save escaped delimiters.
        $value = str_replace("\\$delim", "\x00", $value);
        // Tag unescaped delimiters.
        $value = str_replace("$delim", "\x01", $value);
        // Restore the escaped delimiters.
        $value = str_replace("\x00", "$delim", $value);
        // Split the line on the delimiter tag.
        return explode("\x01", $value);
    }

    function _parseParam($param) {
        $tmp = split_quoted_string('=', $param, 2);
        if (count($tmp) == 1) {
            $value = $tmp[0]; 
            $name = $this->_paramName($value);
            $this->params[$name][] = $value;
        } else {
            $name = $tmp[0];
            $values = split_quoted_string(',', $tmp[1]); 
            foreach ($values as $value) {
                $this->params[$name][] = $value;
            }
        }
    }

    function _paramName($value) {
        static $types = array (
                'DOM', 'INTL', 'POSTAL', 'PARCEL','HOME', 'WORK',
                'PREF', 'VOICE', 'FAX', 'MSG', 'CELL', 'PAGER',
                'BBS', 'MODEM', 'CAR', 'ISDN', 'VIDEO',
                'AOL', 'APPLELINK', 'ATTMAIL', 'CIS', 'EWORLD',
                'INTERNET', 'IBMMAIL', 'MCIMAIL',
                'POWERSHARE', 'PRODIGY', 'TLX', 'X400',
                'GIF', 'CGM', 'WMF', 'BMP', 'MET', 'PMB', 'DIB',
                'PICT', 'TIFF', 'PDF', 'PS', 'JPEG', 'QTIME',
                'MPEG', 'MPEG2', 'AVI',
                'WAVE', 'AIFF', 'PCM',
                'X509', 'PGP');
        static $values = array (
                'INLINE', 'URL', 'CID');
        static $encodings = array (
                '7BIT', 'QUOTED-PRINTABLE', 'BASE64');
        $name = 'UNKNOWN';
        if (in_array($value, $types)) {
            $name = 'TYPE';
        } elseif (in_array($value, $values)) {
            $name = 'VALUE';
        } elseif (in_array($value, $encodings)) {
            $name = 'ENCODING';
        }
        return $name;
    }

    function _decodeQuotedPrintable(&$lines) {
        $value = &$this->value;
        while ($value[strlen($value) - 1] == "=") {
            $value = substr($value, 0, strlen($value) - 1);
            if (!(list(, $line) = each($lines))) {
                break;
            }
            $value .= rtrim($line);
        }
        $value = quoted_printable_decode($value);
    }

}

function split_quoted_string($d, $s, $n = 0) {
    $quote = false;
    $len = strlen($s);
    for ($i = 0; $i < $len && ($n == 0 || $n > 1); $i++) {
        $c = $s{$i};
        if ($c == '"') {
            $quote = !$quote;
        } else if (!$quote && $c == $d) {
            $s{$i} = "\x00";
            if ($n > 0) {
                $n--;
            }
        }
    }
    return explode("\x00", $s);
}
