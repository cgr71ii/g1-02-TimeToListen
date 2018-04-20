<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Group;
use App\Publication;
use DB;

class GroupController extends Controller
{

    private function getFriends($id)
    {
        $users = array();

        if (User::find($id) == null )
        {
            return null;
        }

        $user1 = User::find($id)->following()->get();
        $user2 = User::find($id)->followers()->get();

        foreach ($user1 as $u)
        {
            array_push($users, $u);
        }

        foreach ($user2 as $u)
        {
            array_push($users, $u);
        }

        return $users;
    }


    public function show(Request $request)
    {

        if (session('user') === null)
        {
            return redirect('/');
        }

        /*
        if(request()->has('empty'))
        {
            $friends=[];
            $groups=[];
        }
        else
        {
            $friends = $this->getFriends(session('user')->id);

            $groups = DB::table('group_user')->where('user_id',session('user')->id)->join('groups',function($join){
                $join->on('id','group_id');
            })->get();
            
            
        }
        */

        $friends = session('user')->following()->get();
        $groups = session('user')->group_user();

        if (session('group_field') !== null)
        {
            $groups = $groups->orderBy(session('group_field'), session('group_direction'));
        }
        else if ($request->has('field') && $request->has('direction'))
        {
            session([   'group_field' => $request->field,
                        'group_direction' => $request->direction]);

            $groups = $groups->orderBy($request->field, $request->direction);
        }

        $groups = $groups->simplePaginate(5);

        if ($request->ajax())
        {
            return view('groups.groups-pag', ['groups' => $groups])->render();
        }
        
        return view('groups.groups',compact('friends','groups'));

    }

    public function listGroups(Request $request)
    {
        $groups = Group::where('id', '>=', '0');

        if ($request->has('order-form'))
        {
            session([   'group_field' => null, 
                        'group_direction' => null]);
        }

        if (session('group_field') !== null)
        {
            $groups = $groups->orderBy(session('group_field'), session('group_direction'));
        }
        else if ($request->has('field') && $request->has('direction'))
        {
            session([   'group_field' => $request->field,
                        'group_direction' => $request->direction]);

            $groups = $groups->orderBy($request->field, $request->direction);
        }

        $groups = $groups->simplePaginate(5);

        if ($request->ajax())
        {
            return view('lists.pag.groups', ['groups' => $groups])->render();
        }
        
        return view('lists.list-groups', ['groups' => $groups]);
    }

    public function createGroup(Request $request)
    {
        if($request->friend_list == null || $request->newgroupname == null)
        {
            return back()->with('Error');
        }

        $g = Group::where('name',$request->newgroupname)->first();

        if($g != null)
        {
            $count = DB::table('group_user')->where('user_id',session('user')->id)->where('group_id',$g->id)->count();
        }
        else
        {
            $count = 0;
        }
        
        if($count != 0)
        {
            return back()->with('Error');
        }

        if (in_array("allfriends", $request->friend_list))
        {
            $friends = session('user')->following()->get();

            $group = new Group([
                'creator_id' => session('user')->id,
                'name' => $request->newgroupname
            ]);

            $group->save();

            $group = Group::where('name',$request->newgroupname)->FirstOrFail();

            foreach($friends as $friend)
            {
                $user = User::where('id', $friend->id)->FirstOrFail();
                $user->group_user()->attach($group->id);
            }
        }
        else
        {
            $friends = $request->friend_list; 

            $group = new Group([
                'creator_id' => session('user')->id,
                'name' => $request->newgroupname
            ]);

            $group->save();

            $group = Group::where('name',$request->newgroupname)->FirstOrFail();

            foreach($friends as $friend_id)
            {
                $user = User::where('id', $friend_id)->FirstOrFail();
                $user->group_user()->attach($group->id);
            }
        }
        
        session('user')->group_user()->attach($group->id);

        return redirect('/groups');
    }

    public function addFriend(Request $request)
    {

        $group = Group::where('id',$request->group_id)->first();

        if (in_array("allfriends", $request->friend_list))
        {
            $friends = session('user')->following()->get();

            foreach($friends as $friend)
            {
                $user = User::where('id', $friend->id)->FirstOrFail();

                $count = DB::table('group_user')->where('user_id',$friend->id)->where('group_id',$request->group_id)->count();
                
                if($count < 1)
                {
                    $user->group_user()->attach($group->id);
                }
            }
        }
        else
        {
            $friends = $request->friend_list;

            foreach($friends as $friend_id)
            {
                $user = User::where('id', $friend_id)->FirstOrFail();
                
                $count = DB::table('group_user')->where('user_id',$friend_id)->where('group_id',$request->group_id)->count();
                if($count<1)
                {
                    $user->group_user()->attach($group->id);
                }
            }
        }
        
        return back();
    }

    public function groupPublications(Request $request)
    {
        if ($request->has('group_id'))
        {
            session(['group_id' => $request->group_id]);
        }

        $id = session('group_id');

        $group = Group::find($id);

        $friends = session('user')->following()->get();

        $members = $group->users()->get();

        //$group = $group->get()[0];

        $publications = Publication::where('group_id', $id);

        if ($request->has('order-form'))
        {
            session([   'group_field' => null, 
                        'group_direction' => null]);
        }

        if (session('group_field') !== null)
        {
            $publications = $publications->orderBy(session('group_field'), session('group_direction'));
        }
        else if ($request->has('field') && $request->has('direction'))
        {
            session([   'group_field' => $request->field,
                        'group_direction' => $request->direction]);

            $publications = $publications->orderBy($request->field, $request->direction);
        }

        $publications = $publications->simplePaginate(5);

        if ($request->ajax())
        {
            return view('groups.group-publications-pag', ['publications' => $publications])->render();
            //return view('groups.groupPublications', ['group' => $group, 'group_id' => $id, 'friends' => $friends, 'members' => $members, 'publications' => $publications]);
        }

        return view('groups.groupPublications', ['group' => $group, 'group_id' => $id, 'friends' => $friends, 'members' => $members, 'publications' => $publications]);
    }

    public function exit(Request $request, $id)
    {
        if (session('user') === null)
        {
            return redirect('/');
        }

        if ($request->has('group_id'))
        {
            //$group = Group::find($request->group_id);
            $group = Group::where('id', $request->group_id);

            $count = $group->get()->count();
            
            if ($count == 1)
            {
                if ($group->get()[0]->creator_id == session('user')->id)
                {
                    $group->delete();
                }
                else
                {
                    session('user')->group_user()->detach($request->group_id);
                }

                $groups = session('user')->group_user();

                return back()->with(['groups' => $groups]);
            }
        }

        return back()->with('Error');
        /*
        $toDelete = DB::table('group_user')->where('user_id',session('user')->id)->where('group_id',$id)->first();
        $user = User::where('id',session('user')->id)->FirstOrFail();
        if($toDelete==null)
        {
            return redirect('/groups');
        }
        $user->group_user()->detach($toDelete->group_id);

        $count = DB::table('group_user')->where('group_id',$id)->count();
        //si no hay personas en un grupo se elimina
        if($count<1 || Group::find($id)->creator_id==session('user')->id)
        {
            $group = Group::find($id);
            $group->delete();
        }

        return redirect('/groups');
        */
    }

    public function updateOnlyName(Request $request)
    {
        if (session('user') === null)
        {
            return redirect('/');
        }

        $group = Group::find($request->group_id);

        $group->name = $request->name;

        $group->save();

        return redirect('/groups');
    }

    public function delete(Request $request)
    {
        $group = Group::find($request->group_id)->first();

        $group->delete();

        return back();
    }

    public function showChangeName($id) 
    {
        $count = Group::where('id',$id)->where('creator_id',session('user')->id)->count();

        if($count<1)
        {
            
            return redirect('groups/'.$id);
        }        

        return view('groups.groupNameModification',compact('id'));
    }

    public function changeName(Request $request)
    {
        if( $request->newgroupname!=null)
        {
            $group = Group::find($request->id);
            if($group!=null)
            {
                $group->fill(['name' => $request->newgroupname])->save();
            }
        }
        return redirect('groups/'.$request->id);
        
    }

}